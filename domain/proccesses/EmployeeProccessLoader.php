<?php

namespace domain\proccesses;

use doh\services\classes\ProcessLoader;
use domain\forms\base\DolzhForm;
use domain\forms\base\EmployeeHistoryForm;
use domain\forms\base\ParttimeForm;
use domain\forms\base\PodrazForm;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\forms\ImportEmployeeForm;
use domain\services\base\DolzhService;
use domain\services\base\EmployeeHistoryService;
use domain\services\base\ParttimeService;
use domain\services\base\PersonService;
use domain\services\base\PodrazService;
use domain\services\ProxyService;
use domain\services\Service;
use ngp\services\classes\ChunkReadFilter;
use PHPExcel;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\base\Model;

class EmployeeProccessLoader extends ProcessLoader
{
    const CHUNK_SIZE = 1000;
    const START_ROW = 2;

    const STATUS_GENERAL = 'status_general';
    const STATUS_PART_TIME = 'status_part_time';

    public $description = 'Импорт сотрудников';
    /** @var string */
    private $importFilePath;
    private $executing = true;
    /** @var PHPExcel */
    private $objPHPExcelReport;
    /** @var \PHPExcel_Worksheet */
    private $sheetReport;

    private $success = 0;
    private $error = 0;
    private $rows = 0;
    private $reportRow = 1;

    private $highestRow;

    /**
     * EmployeeProccessLoader constructor.
     * @param string $importFilePath
     * @param array $config
     */
    public function __construct($importFilePath, $config = [])
    {
        $this->importFilePath = $importFilePath;
        $this->objPHPExcelReport = new \PHPExcel();
        $this->sheetReport = $this->objPHPExcelReport->getActiveSheet();
        $this->addReportHeader();
        parent::__construct($config);
    }

    public function body()
    {
        /** @var \PHPExcel_Reader_Excel2007|\PHPExcel_Reader_Excel5 $objReader */
        $objReader = \PHPExcel_IOFactory::createReaderForFile($this->importFilePath);
        $this->highestRow = $this->getHighestRow();

        $chunkFilter = new ChunkReadFilter();
        $objReader->setReadFilter($chunkFilter);
        $objReader->setReadDataOnly(true);

        while ($this->executing) {
            $chunkFilter->setRows(self::START_ROW, self::CHUNK_SIZE);
            $objPHPExcel = $objReader->load($this->importFilePath);
            $objPHPExcel->setActiveSheetIndex(0);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            for ($i = self::START_ROW; $i < self::START_ROW + self::CHUNK_SIZE; $i++) {
                $this->rows++;
                $row = $objWorksheet->rangeToArray('A' . $i . ':N' . $i, null, true, false);
                $row = $row[key($row)];

                if (empty($row[0])) {
                    $this->executing = false;
                    break;
                }

                if ($i % 50 === 0) {
                    $this->addPercentComplete(round($i * 99 / $this->highestRow));
                }

                $form = $this->getForm($row);

                if ($form->validate()) {
                    /** @var PersonService $personService */
                    $personService = new ProxyService(Yii::$container->get('domain\services\base\PersonService'), false);

                    if (!$person = $personService->getUserByINN($form->inn)) {
                        $userForm = new UserForm([
                            'person_fullname' => $form->fio,
                            'person_username' => UserForm::generateUserName($form->fio),
                            'person_password' => 11111111,
                            'person_password_repeat' => 11111111,
                            'assignRoles' => '[]',
                        ]);

                        $profileForm = new ProfileForm(null, [
                            'profile_inn' => $form->inn,
                            'profile_dr' => $form->dr,
                            'profile_pol' => $form->pol,
                            'profile_snils' => $form->snils,
                            'profile_address' => $form->address,
                        ]);

                        if (!$person_id = $personService->create($userForm, $profileForm)) {
                            $this->addReportRow($i, $form, $this->getErrorsFromService($personService) . $this->getErrorsFromForm($userForm) . $this->getErrorsFromForm($profileForm));
                            $this->error++;
                            continue;
                        }
                    } else {
                        $person_id = $person->person_id;
                    }

                    /** @var DolzhService $dolzhService */
                    $dolzhService = new ProxyService(Yii::$container->get('domain\services\base\DolzhService'), false);
                    /** @var PodrazService $podrazService */
                    $podrazService = new ProxyService(Yii::$container->get('domain\services\base\PodrazService'), false);

                    if (!$dolzh = $dolzhService->findByName($form->dolzh)) {
                        $dolzhForm = new DolzhForm(null, ['dolzh_name' => $form->dolzh]);
                        $dolzh_id = $dolzhService->create($dolzhForm);
                    } else {
                        $dolzh_id = $dolzh->dolzh_id;
                    }

                    if (!$podraz = $podrazService->findByName($form->podraz)) {
                        $podrazForm = new PodrazForm(null, ['podraz_name' => $form->podraz]);
                        $podraz_id = $podrazService->create($podrazForm);
                    } else {
                        $podraz_id = $podraz->podraz_id;
                    }

                    if (!$statusEmployee = $this->statusEmployee($form)) {
                        $this->addReportRow($i, $form, 'Не определен статус сутрудника');
                        $this->error++;
                        continue;
                    }

                    if ($statusEmployee === self::STATUS_GENERAL) {
                        file_put_contents('test.txt', "\n" .print_r([$person_id, $dolzh_id, $podraz_id], true), FILE_APPEND);

                        /** @var EmployeeHistoryService $employeeHistoryService */
                        $employeeHistoryService = new ProxyService(Yii::$container->get('domain\services\base\EmployeeHistoryService'), false);
                        $employeeHistoryForm = new EmployeeHistoryForm(null, false, [
                            'person_id' => Uuid::uuid2str($person_id),
                            'dolzh_id' => Uuid::uuid2str($dolzh_id),
                            'podraz_id' => Uuid::uuid2str($podraz_id),
                            'employee_history_begin' => $form->dateBegin,
                            'assignBuilds' => '[]',
                        ]);

                        if ($employeeHistoryService->create($employeeHistoryForm)) {
                            $this->success++;
                        } else {
                            $this->addReportRow($i, $form, $this->getErrorsFromService($employeeHistoryService) . $this->getErrorsFromForm($employeeHistoryForm));
                            $this->error++;
                            continue;
                        }
                    } elseif ($statusEmployee === self::STATUS_PART_TIME) {
                        /** @var ParttimeService $parttimeService */
                        $parttimeService = new ProxyService(Yii::$container->get('domain\services\base\ParttimeService'), false);
                        $parttimeForm = new ParttimeForm(null, false, [
                            'person_id' => Uuid::uuid2str($person_id),
                            'dolzh_id' => Uuid::uuid2str($dolzh_id),
                            'podraz_id' => Uuid::uuid2str($podraz_id),
                            'parttime_begin' => $form->dateBegin,
                            'parttime_end' => $form->dateEnd,
                            'assignBuilds' => '[]',
                        ]);

                        if ($parttimeService->create($parttimeForm)) {
                            $this->success++;
                        } else {
                            $this->addReportRow($i, $form, $this->getErrorsFromService($parttimeService) . $this->getErrorsFromForm($parttimeForm));
                            $this->error++;
                            continue;
                        }
                    }
                } else {
                    $this->addReportRow($i, $form, $this->getErrorsFromForm($form));
                    $this->error++;
                };
            }
        }

        if ($this->error) {
            /** @var \PHPExcel_Writer_CSV $objWriter */
            $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcelReport, 'CSV');
            $objWriter->setDelimiter(';');
            $reportPath = Yii::getAlias('@common/ftpimport/reports/' . date('Y-m-d') . time() . '.csv');
            $objWriter->save($reportPath);
            file_put_contents($reportPath, mb_convert_encoding(file_get_contents($reportPath), 'windows-1251', 'UTF-8'));
            $this->addFile($reportPath, 'Результат импорта.csv');
        }

        $successPercent = round($this->success * 100 / $this->rows, 1);
        $errorPercent = round($this->error * 100 / $this->rows, 1);
        $this->addShortReport("Итоги обработки:\n- Всего записей: {$this->rows};\n- Успешно ($successPercent%): {$this->success};\n- Ошибок ($errorPercent%): {$this->error};");
//            file_put_contents('test.txt', 'goood', FILE_APPEND);
    }

    protected function addReportHeader()
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, 'Номер');
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, 'Текст ошибки');
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, 'Данные');
        $this->reportRow++;
    }

    protected function addReportRow($i, ImportEmployeeForm $form, $message)
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, $i);
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, $message);
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, implode('; ', [
            $form->period,
            $form->fio,
            $form->dr,
            $form->pol,
            $form->snils,
            $form->inn,
            $form->dolzh,
            $form->status,
            $form->podraz,
            $form->dateBegin,
            $form->dateEnd,
            $form->address,
        ]));
        $this->reportRow++;
    }

    protected function getHighestRow()
    {
        /** @var \PHPExcel_Reader_Excel2007|\PHPExcel_Reader_Excel5 $objReader */
        $objReader = \PHPExcel_IOFactory::createReaderForFile($this->importFilePath);
        $objPHPExcel = $objReader->load($this->importFilePath);
        $highestRow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        unset($objPHPExcel);
        unset($objReader);
        return $highestRow;
    }

    protected function getForm(array $row)
    {
        return new ImportEmployeeForm([
            'period' => $row[0],
            'fio' => $row[1],
            'dr' => $row[2],
            'pol' => $row[3],
            'snils' => $row[4],
            'inn' => $row[5],
            'dolzh' => $row[6],
            'status' => $row[7],
            'podraz' => $row[9],
            'dateBegin' => $row[10],
            'dateEnd' => $row[11],
            'address' => $row[12],
        ]);
    }

    protected function getErrorsFromService(ProxyService $service)
    {
        return implode(',', $service->getErrorsProxyService());
    }

    protected function getErrorsFromForm(Model $form)
    {
        return implode(',', array_map(function ($errors) {
            return implode(',', $errors);
        }, $form->getErrors()));
    }

    protected function statusEmployee($form)
    {
        if ($form->status === 'Основное место работы') {
            return self::STATUS_GENERAL;
        }

        if (mb_strpos($form->status, 'Совместительство', 0, 'UTF-8') === 0) {
            return self::STATUS_PART_TIME;
        }

        return false;
    }
}