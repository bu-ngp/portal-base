<?php

namespace domain\proccesses;

use doh\services\classes\ProcessLoader;
use domain\forms\base\DolzhForm;
use domain\forms\base\EmployeeHistoryForm;
use domain\forms\base\ParttimeForm;
use domain\forms\base\PodrazForm;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\forms\base\UserFormUpdate;
use domain\forms\ImportEmployeeForm;
use domain\forms\ImportEmployeeOrigForm;
use domain\models\base\EmployeeHistory;
use domain\models\base\Person;
use domain\services\base\DolzhService;
use domain\services\base\EmployeeHistoryService;
use domain\services\base\ParttimeService;
use domain\services\base\PersonService;
use domain\services\base\PodrazService;
use domain\services\ProxyService;
use domain\services\TransactionManager;
use ngp\services\classes\ChunkReadFilter;
use PHPExcel;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\base\Model;
use yii\db\Exception;

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
    /** @var PersonService */
    private $personService;
    /** @var DolzhService */
    private $dolzhService;
    /** @var PodrazService */
    private $podrazService;
    /** @var EmployeeHistoryService */
    private $employeeService;
    /** @var ParttimeService */
    private $parttimeService;
    /** @var  TransactionManager */
    private $transactionManager;

    private $added = 0;
    private $changed = 0;
    private $error = 0;
    private $rows = 0;
    private $reportRow = 1;

    private $highestRow;
    /** @var ImportEmployeeOrigForm */
    private $formOrigData;
    private $currentRow;

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

        $this->personService = new ProxyService(Yii::$container->get('domain\services\base\PersonService'), false);
        $this->dolzhService = new ProxyService(Yii::$container->get('domain\services\base\DolzhService'), false);
        $this->podrazService = new ProxyService(Yii::$container->get('domain\services\base\PodrazService'), false);
        $this->employeeService = new ProxyService(Yii::$container->get('domain\services\base\EmployeeHistoryService'), false);
        $this->parttimeService = new ProxyService(Yii::$container->get('domain\services\base\ParttimeService'), false);
        $this->transactionManager = Yii::$container->get('domain\services\TransactionManager');

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
            for ($this->currentRow = self::START_ROW; $this->currentRow < self::START_ROW + self::CHUNK_SIZE; $this->currentRow++) {
                $this->rows++;
                $row = $this->getExcelRow($objWorksheet);

                if ($this->isEnd($row)) {
                    break;
                }

                $this->calculatePercentCompleted();
                $form = $this->getForm($row);
                $this->formOrigData = $this->getFormOriginalData($row);
                $this->formOrigData->validate();

                if ($form->validate()) {

                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if (($person_id = $this->makePerson($form)) === false) {
                            $transaction->rollBack();
                            continue;
                        }
                        if (($dolzh_id = $this->makeDolzh($form)) === false) {
                            $transaction->rollBack();
                            continue;
                        }
                        if (($podraz_id = $this->makePodraz($form)) === false) {
                            $transaction->rollBack();
                            continue;
                        }
                        //file_put_contents('test.txt', print_r([$person_id, $dolzh_id, $podraz_id], true), FILE_APPEND);
                        // print_r([$person_id, $dolzh_id, $podraz_id]);
                        switch ($this->statusEmployee($form)) {
                            case self::STATUS_GENERAL:
                                if (!$this->makeEmployee($form, $person_id, $dolzh_id, $podraz_id)) {
                                    $transaction->rollBack();
                                    continue;
                                }
                                break;
                            case self::STATUS_PART_TIME:
//                                if (!$this->makeParttime($form, $person_id, $dolzh_id, $podraz_id)) {
//                                    $transaction->rollBack();
//                                    continue;
//                                }
                                break;
                            default:
                                $transaction->rollBack();
                                $this->addReportRow('Не определен статус сутрудника');
                                $this->error++;
                        }

                        $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                } else {
                    $this->addReportRow($this->getErrorsFromForm($form));
                    $this->error++;
                };
            }
        }

        if ($this->error || $this->changed) {
            $this->saveReport();
        }
        $this->addItog();
        //file_put_contents('test.txt', 'goood', FILE_APPEND);
    }

    protected function addReportHeader()
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, 'Номер');
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, 'Текст ошибки');
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, 'Данные');
        $this->reportRow++;
    }

    protected function addReportRow($message)
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, $this->currentRow);
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, $message);
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, implode('; ', [
            $this->formOrigData->period,
            $this->formOrigData->fio,
            $this->formOrigData->dr,
            $this->formOrigData->pol,
            $this->formOrigData->snils,
            $this->formOrigData->inn,
            $this->formOrigData->dolzh,
            $this->formOrigData->status,
            $this->formOrigData->podraz,
            $this->formOrigData->dateBegin,
            $this->formOrigData->dateEnd,
            $this->formOrigData->address,
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

    protected function getFormOriginalData(array $row)
    {
        return new ImportEmployeeOrigForm([
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
        return implode(',', $service->getErrorsProxyService(true));
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

    protected function getExcelRow(\PHPExcel_Worksheet $worksheet)
    {
        $row = $worksheet->rangeToArray('A' . $this->currentRow . ':N' . $this->currentRow, null, true, false);
        return $row[key($row)];
    }

    protected function isEnd(array $row)
    {
        if (empty($row[0])) {
            $this->executing = false;
            return true;
        }

        return false;
    }

    protected function calculatePercentCompleted()
    {
        if ($this->currentRow % 50 === 0) {
            $this->addPercentComplete(round($this->currentRow * 99 / $this->highestRow));
        }
    }

    protected function makePerson(ImportEmployeeForm $form)
    {
        if (!$person = $this->personService->getUserByINN($form->inn)) {
            if (($person_id = $this->createPerson($form)) === false) {
                return false;
            }
        } else {
            if (($person_id = $this->updatePerson($person, $form)) === false) {
                return false;
            }
        }

        return Uuid::uuid2str($person_id);
    }

    protected function createPerson(ImportEmployeeForm $form)
    {
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

        return $this->createByService($this->personService, [$userForm, $profileForm]);
    }

    protected function updatePerson(Person $person, ImportEmployeeForm $form)
    {
        $fromUserForm = $person->attributes;
        $fromUserForm['person_fullname'] = $form->fio;
        $diffUser = array_diff_assoc($fromUserForm, $person->attributes);

        $profile = $this->personService->getProfile($person->primaryKey);
        $fromProfileForm = $profile->attributes;
        $fromProfileForm['profile_dr'] = $form->dr;
        $fromProfileForm['profile_pol'] = $form->pol;
        $fromProfileForm['profile_snils'] = $form->snils;
        $fromProfileForm['profile_address'] = $form->address;
        $diffProfile = array_diff_assoc($fromProfileForm, $profile->attributes);

        $diff = array_merge($diffUser, $diffProfile);
        $diffWas = array_merge(array_diff_assoc($profile->attributes, $fromProfileForm), array_diff_assoc($person->attributes, $fromUserForm));

        if ($diff) {
            $userFormUpdate = new UserFormUpdate($person, $diffUser);
            $profileForm = new ProfileForm($profile, $diffProfile);

            if ($this->updateByService($this->personService, [$person->primaryKey, $userFormUpdate, $profileForm], $diff, $diffWas) === false) {
                return false;
            };
        }

        return $person->person_id;
    }

    protected function makeDolzh(ImportEmployeeForm $form)
    {
        if (!$dolzh_id = $this->dolzhService->findIDByName($form->dolzh)) {
            if (($dolzh_id = $this->createDolzh($form)) === false) {
                return false;
            }
        }

        return Uuid::uuid2str($dolzh_id);
    }

    protected function createDolzh(ImportEmployeeForm $form)
    {
        $dolzhForm = new DolzhForm(null, ['dolzh_name' => $form->dolzh]);
        if ($this->createByService($this->dolzhService, [$dolzhForm])) {
            return $this->dolzhService->findIDByName($form->dolzh);
        }

        return false;
    }

    protected function makePodraz(ImportEmployeeForm $form)
    {
        if (!$podraz_id = $this->podrazService->findIDByName($form->podraz)) {
            if (($podraz_id = $this->createPodraz($form)) === false) {
                return false;
            }
        }

        return Uuid::uuid2str($podraz_id);
    }

    protected function createPodraz(ImportEmployeeForm $form)
    {
        $podrazForm = new PodrazForm(null, ['podraz_name' => $form->podraz]);
        if ($this->createByService($this->podrazService, [$podrazForm])) {
            return $this->podrazService->findIDByName($form->podraz);
        }

        return false;
    }

    protected function makeEmployee(ImportEmployeeForm $form, $person_id, $dolzh_id, $podraz_id)
    {
        if (!($employee = $this->employeeService->getEmployeeByDate($form->dateBegin))) {
            if ($result = $this->createEmployee($form, $person_id, $dolzh_id, $podraz_id)) {
                $this->added++;
            }
        } else {
            if ($result = $this->updateEmployee($employee, $form, $person_id, $dolzh_id, $podraz_id)) {
                $this->changed++;
            }
        }

        return $result;
    }

    protected function makeParttime(ImportEmployeeForm $form, $person_id, $dolzh_id, $podraz_id)
    {
        /** @var ParttimeService $parttimeService */
        $parttimeService = new ProxyService(Yii::$container->get('domain\services\base\ParttimeService'), false);
        $parttimeForm = new ParttimeForm(null, false, [
            'person_id' => $person_id,
            'dolzh_id' => $dolzh_id,
            'podraz_id' => $podraz_id,
            'parttime_begin' => $form->dateBegin,
            'parttime_end' => $form->dateEnd,
            'assignBuilds' => '[]',
        ]);

        if ($parttimeService->create($parttimeForm)) {
            $this->added++;
        } else {
            $this->addReportRow($this->getErrorsFromService($parttimeService) . $this->getErrorsFromForm($parttimeForm));
            $this->error++;
        }
    }

    protected function saveReport()
    {
        /** @var \PHPExcel_Writer_CSV $objWriter */
        $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcelReport, 'CSV');
        $objWriter->setDelimiter(';');
        $reportPath = Yii::getAlias('@common/ftpimport/reports/' . date('Y-m-d') . time() . '.csv');
        $objWriter->save($reportPath);
        file_put_contents($reportPath, mb_convert_encoding(file_get_contents($reportPath), 'windows-1251', 'UTF-8'));
        $this->addFile($reportPath, 'Результат импорта.csv');
    }

    protected function addItog()
    {
        $this->rows--;
        $addedPercent = round($this->added * 100 / $this->rows, 1);
        $changedPercent = round($this->changed * 100 / $this->rows, 1);
        $errorPercent = round($this->error * 100 / $this->rows, 1);
        $this->addShortReport("Итоги обработки:\n- Всего записей: {$this->rows};\n- Добавлено ($addedPercent%): {$this->added};\n- Изменено ($changedPercent%): {$this->changed};\n- Ошибок ($errorPercent%): {$this->error};");
    }

    protected function createByService(ProxyService $service, array $forms)
    {
        $result = call_user_func_array([$service, 'create'], $forms);

        if ($result) {
            return $result;
        } else {
            $this->addReportRow($this->getMessageFromServiceAndForms($service, $forms));
            $this->error++;
            return false;
        }
    }

    protected function getMessageFromServiceAndForms(ProxyService $service, array $forms)
    {
        $that = $this;
        return implode('; ', [$this->getErrorsFromService($service), implode('; ', array_map(function ($form) use ($that) {
            return $that->getErrorsFromForm($form);
        }, $forms))]);
    }

    protected function updateByService(ProxyService $service, array $params, array $diff, array $diffWas)
    {
        $result = call_user_func_array([$service, 'update'], $params);

        if ($result) {
            $this->addReportRow($this->getMessageFromDiff($diff, $diffWas));
            $this->changed++;
            return $result;
        } else {
            $this->addReportRow($this->getMessageFromServiceAndForms($service, $params));
            $this->error++;
            return false;
        }
    }

    protected function getMessageFromDiff(array $diff, array $diffWas)
    {
        return "Запись именена: " . implode('; ', array_map(function ($attr, $now) use ($diffWas) {
                return "[$attr][Было:{$diffWas[$attr]}][Стало:$now]";
            }, array_keys($diff), $diff));
    }

    protected function createEmployee($form, $person_id, $dolzh_id, $podraz_id)
    {
        $employeeHistoryForm = new EmployeeHistoryForm(null, false, [
            'person_id' => $person_id,
            'dolzh_id' => $dolzh_id,
            'podraz_id' => $podraz_id,
            'employee_history_begin' => $form->dateBegin,
            'assignBuilds' => '[]',
        ]);

        return $this->createByService($this->employeeService, [$employeeHistoryForm]);
    }

    protected function updateEmployee(EmployeeHistory $employee, ImportEmployeeForm $form, $person_id, $dolzh_id, $podraz_id)
    {
        if ($this->employeeExist($employee, $person_id, $dolzh_id, $podraz_id)) {
            if ($this->differenceEmployeeDateEnd($employee, $form->dateEnd)) {
                return $this->updateEmployeeDateEnd($employee, $form->dateEnd);
            }

            return true;
        } else {
            return $this->updateCurrentEmployee($employee, $person_id, $dolzh_id, $podraz_id)
                && $this->updateEmployeeDateEnd($employee, $form->dateEnd);
        }
    }

    protected function employeeExist(EmployeeHistory $employee, $person_id, $dolzh_id, $podraz_id)
    {
        return $employee->person_id === Uuid::str2uuid($person_id)
            && $employee->dolzh_id === Uuid::str2uuid($dolzh_id)
            && $employee->podraz_id === Uuid::str2uuid($podraz_id);
    }

    protected function differenceEmployeeDateEnd(EmployeeHistory $employee, $dateEnd)
    {
        return $employee->person->person_fired != $dateEnd;
    }

    protected function updateEmployeeDateEnd(EmployeeHistory $employee, $dateEnd)
    {
        $diffWas = ['person_fired' => $employee->person->person_fired];
        $diff = ['person_fired' => $dateEnd];
        $userFormUpdate = new UserFormUpdate($employee->person, ['person_fired' => $dateEnd]);
        $profile = $this->personService->getProfile($employee->person->primaryKey);
        $profileForm = new ProfileForm($profile, []);

        return $this->updateByService($this->personService, [$employee->person_id, $userFormUpdate, $profileForm], $diff, $diffWas);
    }

    protected function updateCurrentEmployee(EmployeeHistory $employee, $person_id, $dolzh_id, $podraz_id)
    {
        $employeeForm = $employee->attributes;
        $employeeForm['person_id'] = Uuid::str2uuid($person_id);
        $employeeForm['dolzh_id'] = Uuid::str2uuid($dolzh_id);
        $employeeForm['podraz_id'] = Uuid::str2uuid($podraz_id);

        $diff = array_diff_assoc($employeeForm, $employee->attributes);
        $diffWas = array_diff_assoc($employee->attributes, $employeeForm);

        if ($diff) {
            $employeeHistoryForm = new EmployeeHistoryForm($employee, false, $diff);

            if ($this->updateByService($this->employeeService, [$employee->primaryKey, $employeeHistoryForm], $diff, $diffWas) === false) {
                return false;
            };
        }

        return true;
    }
}