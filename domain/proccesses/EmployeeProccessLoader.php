<?php

namespace domain\proccesses;

use doh\services\classes\ProcessLoader;
use domain\forms\base\DolzhForm;
use domain\forms\base\EmployeeHistoryForm;
use domain\forms\base\EmployeeHistoryUpdateForm;
use domain\forms\base\ParttimeForm;
use domain\forms\base\ParttimeUpdateForm;
use domain\forms\base\PodrazForm;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\forms\base\UserFormUpdate;
use domain\forms\ImportEmployeeForm;
use domain\forms\ImportEmployeeOrigForm;
use domain\models\base\Dolzh;
use domain\models\base\EmployeeHistory;
use domain\models\base\Parttime;
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
use Yii;
use yii\base\Model;

class EmployeeProccessLoader extends ProcessLoader
{
    const CHUNK_SIZE = 1000;

    const STATUS_GENERAL = 'status_general';
    const STATUS_PART_TIME = 'status_part_time';

    const REPORT_STATUS_ADDED = 'Добавлено';
    const REPORT_STATUS_CHANGED = 'Изменено';
    const REPORT_STATUS_UNCHANGED = 'Без изменений';
    const REPORT_STATUS_ERROR = 'Ошибка';

    public $description = 'Импорт сотрудников';
    private $startRow = 2;
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

    private $reportStatus;

    private $reportPath;
    private $reportRowCountBuffer = 0;
    private $reportName;

    /**
     * EmployeeProccessLoader constructor.
     * @param string $importFilePath
     * @param array $config
     */
    public function __construct($importFilePath, $config = [])
    {
        $this->importFilePath = $importFilePath;
        $this->reportName = date('Y-m-d') . time() . '.xlsx';
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
            $chunkFilter->setRows($this->startRow, self::CHUNK_SIZE);
            $objPHPExcel = $objReader->load($this->importFilePath);
            $objPHPExcel->setActiveSheetIndex(0);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            for ($this->currentRow = $this->startRow; $this->currentRow < $this->startRow + self::CHUNK_SIZE; $this->currentRow++) {
                $this->rows++;
                $this->reportStatus = self::REPORT_STATUS_UNCHANGED;
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
                            $this->error++;
                            $transaction->rollBack();
                            continue;
                        }
                        if (($dolzh_id = $this->makeDolzh($form)) === false) {
                            $this->error++;
                            $transaction->rollBack();
                            continue;
                        }
                        if (($podraz_id = $this->makePodraz($form)) === false) {
                            $this->error++;
                            $transaction->rollBack();
                            continue;
                        }
                        //file_put_contents('test.txt', print_r([$person_id, $dolzh_id, $podraz_id], true), FILE_APPEND);
                        // print_r([$person_id, $dolzh_id, $podraz_id]);
                        switch ($this->statusEmployee($form)) {
                            case self::STATUS_GENERAL:
                                if (!$this->makeEmployee($form, $person_id, $dolzh_id, $podraz_id)) {
                                    $this->error++;
                                    $transaction->rollBack();
                                    continue 2;
                                }
                                break;
                            case self::STATUS_PART_TIME:
                                if (!$this->makeParttime($form, $person_id, $dolzh_id, $podraz_id)) {
                                    $this->error++;
                                    $transaction->rollBack();
                                    continue 2;
                                }
                                break;
                            default:
                                $this->error++;
                                $transaction->rollBack();
                                $this->addReportRow(self::REPORT_STATUS_ERROR, 'Не определен статус сутрудника');
                                continue 2;
                        }

                        switch ($this->reportStatus) {
                            case self::REPORT_STATUS_ADDED:
                                $this->added++;
                                break;
                            case self::REPORT_STATUS_CHANGED:
                                $this->changed++;
                                break;
                        }

                        $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                } else {
                    $this->error++;
                    $this->addReportRow(self::REPORT_STATUS_ERROR, $this->getErrorsFromForm($form));
                };
            }
            $this->startRow += self::CHUNK_SIZE;
        }

        $notFounded = false;
        if ($this->fireNotFoundPersons() | $this->closeNotFoundParttimes()) {
            $notFounded = true;
            $this->resetForImportFields();
        }

        if ($notFounded || $this->error || $this->changed || $this->added) {
            $this->finishFormatReport();
            $this->saveReport();
            $this->addFile($this->reportPath, 'Результат импорта.xlsx');
        }
        $this->addItog();
        //file_put_contents('test.txt', 'goood', FILE_APPEND);
    }

    protected function addReportHeader()
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, 'Номер');
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, 'Результат');
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, 'Сообщение');
        $this->sheetReport->setCellValueByColumnAndRow(3, $this->reportRow, 'Данные');
        $this->reportRow++;
    }

    protected function addReportRow($status, $message, $customSourceString = null)
    {
        $this->sheetReport->setCellValueByColumnAndRow(0, $this->reportRow, $customSourceString ? 0 : $this->currentRow);
        $this->sheetReport->setCellValueByColumnAndRow(1, $this->reportRow, $status);
        $this->sheetReport->setCellValueByColumnAndRow(2, $this->reportRow, $message);
        $this->sheetReport->setCellValueByColumnAndRow(3, $this->reportRow, $customSourceString ?: implode('; ', [
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
        $this->reportRowCountBuffer++;

        if ($this->reportRowCountBuffer > 50) {
            $this->saveReport();
            $this->reportRowCountBuffer = 0;
        }
    }

    protected function saveReport()
    {
        if (!$this->reportPath) {
            $this->reportPath = Yii::getAlias('@common/ftpimport/reports/' . $this->reportName);
        }

        $this->appendToFileReport();
    }

    protected function appendToFileReport()
    {
        /** @var \PHPExcel_Writer_Excel2007 $objWriter */
        $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcelReport, 'Excel2007');
        $objWriter->save($this->reportPath);
        /** @var \PHPExcel_Reader_Excel2007|\PHPExcel_Reader_Excel5 $objReader */
        $objReader = \PHPExcel_IOFactory::createReaderForFile($this->reportPath);
        $this->objPHPExcelReport = $objReader->load($this->reportPath);
        $this->sheetReport = $this->objPHPExcelReport->getActiveSheet();
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
            'podraz' => $row[8],
            'dateBegin' => $row[9],
            'dateEnd' => $row[10],
            'address' => $row[11],
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
            'podraz' => $row[8],
            'dateBegin' => $row[9],
            'dateEnd' => $row[10],
            'address' => $row[11],
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
        if (in_array($form->status, ['Основное место работы'])) {
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
        $this->markPerson($person_id);

        return $person_id;
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

        if ($result = $this->createByService($this->personService, [$userForm, $profileForm])) {
            $this->reportStatus = self::REPORT_STATUS_ADDED;
            $this->addReportRow(self::REPORT_STATUS_ADDED, "Добавлен сотрудник '{$userForm->person_fullname}'");
        }

        return $result;
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
            } else {
                $this->reportStatus = self::REPORT_STATUS_CHANGED;
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

        return $dolzh_id;
    }

    protected function createDolzh(ImportEmployeeForm $form)
    {
        $dolzhForm = new DolzhForm(null, ['dolzh_name' => $form->dolzh]);
        if ($this->createByService($this->dolzhService, [$dolzhForm])) {
            $this->reportStatus === self::REPORT_STATUS_UNCHANGED ? self::REPORT_STATUS_CHANGED : $this->reportStatus;
            $this->addReportRow(self::REPORT_STATUS_ADDED, "Добавлена должность '{$dolzhForm->dolzh_name}'");
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

        return $podraz_id;
    }

    protected function createPodraz(ImportEmployeeForm $form)
    {
        $podrazForm = new PodrazForm(null, ['podraz_name' => $form->podraz]);
        if ($this->createByService($this->podrazService, [$podrazForm])) {
            $this->reportStatus === self::REPORT_STATUS_UNCHANGED ? self::REPORT_STATUS_CHANGED : $this->reportStatus;
            $this->addReportRow(self::REPORT_STATUS_ADDED, "Добавлено подразделение '{$podrazForm->podraz_name}'");
            return $this->podrazService->findIDByName($form->podraz);
        }

        return false;
    }

    protected function makeEmployee(ImportEmployeeForm $form, $person_id, $dolzh_id, $podraz_id)
    {
        $result = true;
        if ($employee = $this->employeeExist($person_id)) {
            if ($this->isEmployeesNotEqual([$employee->dolzh_id, $employee->podraz_id], [$dolzh_id, $podraz_id])) {
                $result = $this->isActualImportEmployee($form->period, $employee->employee_history_begin)
                    ? $this->addEmployee($person_id, $dolzh_id, $podraz_id, $form->period)
                    : $this->editEmployee($employee, $person_id, $dolzh_id, $podraz_id, $form->period);
            }
        } else {
            $result = $this->addEmployee($person_id, $dolzh_id, $podraz_id, $form->dateBegin);
        }

        if (!$result) {
            return false;
        }

        if ($this->isEmployeeFiredNotEqual($person_id, $form->dateEnd)) {
            return $this->changeEmployeeFireDate($person_id, $form->dateEnd);
        }

        return $result;
    }

    protected function employeeExist($person_id)
    {
        return $this->employeeService->getCurrentEmployeeByPerson($person_id);
    }

    protected function isEmployeesNotEqual(array $employeeCurrent, array $employeeImport)
    {
        return (bool)array_diff($employeeCurrent, $employeeImport);
    }

    protected function isActualImportEmployee($employeeImportDate, $employeeCurrentDate)
    {
        return (new \DateTime($employeeImportDate)) > (new \DateTime($employeeCurrentDate));
    }

    protected function addEmployee($person_id, $dolzh_id, $podraz_id, $employee_date)
    {
        $employeeHistoryForm = new EmployeeHistoryForm([
            'person_id' => $person_id,
            'dolzh_id' => $dolzh_id,
            'podraz_id' => $podraz_id,
            'employee_history_begin' => $employee_date,
            'assignBuilds' => '[]',
        ]);

        if ($result = $this->createByService($this->employeeService, [$employeeHistoryForm])) {
            $this->reportStatus = self::REPORT_STATUS_ADDED;
            $dateBegin = Yii::$app->formatter->asDate($employee_date);
            $dolzhName = Dolzh::findOne($dolzh_id)->dolzh_name;
            $this->addReportRow(self::REPORT_STATUS_ADDED, "Добавлена основная специальность '$dolzhName' на дату '$dateBegin'");
        }

        return $result;
    }

    protected function editEmployee(EmployeeHistory $employeeHistory, $person_id, $dolzh_id, $podraz_id, $employee_date)
    {
        $employeeForm = $employeeHistory->attributes;
        $employeeForm['person_id'] = $person_id;
        $employeeForm['dolzh_id'] = $dolzh_id;
        $employeeForm['podraz_id'] = $podraz_id;
        $employeeForm['employee_history_begin'] = $employee_date;

        $diff = array_diff_assoc($employeeForm, $employeeHistory->attributes);
        $diffWas = array_diff_assoc($employeeHistory->attributes, $employeeForm);

        if ($diff) {
            $employeeHistoryForm = new EmployeeHistoryUpdateForm($employeeHistory, $diff);
            $this->convertUUIDFields($diff);
            $this->convertUUIDFields($diffWas);

            if ($this->updateByService($this->employeeService, [$employeeHistory->primaryKey, $employeeHistoryForm], $diff, $diffWas) === false) {
                return false;
            };

            $this->reportStatus = self::REPORT_STATUS_CHANGED;
        }

        return true;
    }

    protected function isEmployeeFiredNotEqual($person_id, $dateEnd)
    {
        $person = $this->personService->getUser($person_id);
        return $person->person_fired !== $dateEnd;
    }

    protected function changeEmployeeFireDate($person_id, $dateEnd)
    {
        $person = $this->personService->getUser($person_id);
        $diffWas = ['person_fired' => $person->person_fired];
        $diff = ['person_fired' => $dateEnd];

        $userFormUpdate = new UserFormUpdate($person, ['person_fired' => $dateEnd]);
        $profile = $this->personService->getProfile($person->primaryKey);
        $profileForm = new ProfileForm($profile, []);

        return $this->updateByService($this->personService, [$person->primaryKey, $userFormUpdate, $profileForm], $diff, $diffWas);
    }

    protected function makeParttime(ImportEmployeeForm $form, $person_id, $dolzh_id, $podraz_id)
    {
        $result = true;
        if ($parttime = $this->parttimeExist($person_id, $dolzh_id, $podraz_id, $form->dateBegin)) {
            $parttime_id = $parttime->primaryKey;
            if ($this->isParttimeDateEndNotEqual($parttime->parttime_end, $form->dateEnd)) {
                $result = $this->changeParttimeDateEnd($parttime, $form->dateEnd);
            }
        } else {
            $parttime_id = $this->addParttime($person_id, $dolzh_id, $podraz_id, $form->dateBegin, $form->dateEnd);
            $result = $parttime_id;
        }
        $this->markParttime($parttime_id);

        return $result;
    }

    protected function parttimeExist($person_id, $dolzh_id, $podraz_id, $dateBegin)
    {
        return $this->parttimeService->findByAttributes($person_id, $dolzh_id, $podraz_id, $dateBegin);
    }

    protected function isParttimeDateEndNotEqual($dateEndParttime, $dateEndImportFile)
    {
        return $dateEndParttime !== $dateEndImportFile;
    }

    protected function changeParttimeDateEnd(Parttime $parttime, $dateEnd)
    {
        $parttimeForm = $parttime->attributes;
        $parttimeForm['parttime_end'] = $dateEnd;

        $diff = array_diff_assoc($parttimeForm, $parttime->attributes);
        $diffWas = array_diff_assoc($parttime->attributes, $parttimeForm);

        if ($diff) {
            $parttimeCurrentForm = new ParttimeUpdateForm($parttime, $diff);

            if ($this->updateByService($this->parttimeService, [$parttime->primaryKey, $parttimeCurrentForm], $diff, $diffWas) === false) {
                return false;
            };

            $this->reportStatus = self::REPORT_STATUS_CHANGED;
        }

        return true;
    }

    protected function addParttime($person_id, $dolzh_id, $podraz_id, $dateBegin, $dateEnd)
    {
        $parttimeForm = new ParttimeForm([
            'person_id' => $person_id,
            'dolzh_id' => $dolzh_id,
            'podraz_id' => $podraz_id,
            'parttime_begin' => $dateBegin,
            'parttime_end' => $dateEnd,
            'assignBuilds' => '[]',
        ]);

        if ($result = $this->createByService($this->parttimeService, [$parttimeForm])) {
            $this->reportStatus = self::REPORT_STATUS_ADDED;
            $dateBegin = Yii::$app->formatter->asDate($dateBegin);
            $dolzhName = Dolzh::findOne($dolzh_id)->dolzh_name;
            $this->addReportRow(self::REPORT_STATUS_ADDED, "Добавлено совместительство '$dolzhName' на дату '$dateBegin'");
        }

        return $result;
    }

    protected function addItog()
    {
        $this->rows--;
        $addedPercent = round($this->added * 100 / $this->rows, 1);
        $changedPercent = round($this->changed * 100 / $this->rows, 1);
        $errorPercent = round($this->error * 100 / $this->rows, 1);
        $this->addShortReport("Итоги обработки:\n- Всего записей: {$this->rows};\n- Добавлено ($addedPercent%): {$this->added};\n- Изменено ($changedPercent%): {$this->changed};\n- Ошибок ($errorPercent%): {$this->error};");
        $this->data['result'] = [
            'rows' => $this->rows,
            'added' => $this->added,
            'changed' => $this->changed,
            'error' => $this->error,
            'addedPercent' => $addedPercent,
            'changedPercent' => $changedPercent,
            'errorPercent' => $errorPercent,
            'reportPath' => $this->reportPath,
        ];
    }

    protected function createByService(ProxyService $service, array $params)
    {
        $result = call_user_func_array([$service, 'create'], $params);

        if ($result) {
            return $result;
        } else {
            $params = array_filter($params, function ($param) {
                return $param instanceof Model;
            });
            $this->addReportRow(self::REPORT_STATUS_ERROR, $this->getMessageFromServiceAndForms($service, $params));
            return false;
        }
    }

    protected function getMessageFromServiceAndForms(ProxyService $service, array $forms)
    {
        $that = $this;
        return implode('; ', array_filter([$this->getErrorsFromService($service), implode('; ', array_filter(array_map(function ($form) use ($that) {
            return $that->getErrorsFromForm($form);
        }, $forms)))]));
    }

    protected function updateByService(ProxyService $service, array $params, array $diff, array $diffWas, $customSourceString = null)
    {
        $result = call_user_func_array([$service, 'update'], $params);

        if ($result) {
            $this->addReportRow(self::REPORT_STATUS_CHANGED, $this->getMessageFromDiff($diff, $diffWas), $customSourceString);
            return $result;
        } else {
            $params = array_filter($params, function ($param) {
                return $param instanceof Model;
            });
            $this->addReportRow(self::REPORT_STATUS_ERROR, $this->getMessageFromServiceAndForms($service, $params), $customSourceString);
            return false;
        }
    }

    protected function getMessageFromDiff(array $diff, array $diffWas)
    {
        return "Запись именена: " . implode('; ', array_map(function ($attr, $now) use ($diffWas) {
                return "[$attr][Было:{$diffWas[$attr]}][Стало:$now]";
            }, array_keys($diff), $diff));
    }

    protected function finishFormatReport()
    {
        for ($i = 1; $i <= 4; $i++) {
            $this->objPHPExcelReport->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
        }
    }

    protected function convertUUIDFields(&$diff)
    {
        foreach ($diff as $attribute => $value) {
            switch ($attribute) {
                case 'person_id':
                    $diff[$attribute] = $this->personService->getUser($value)->person_fullname;
                    break;
                case 'dolzh_id':
                    $diff[$attribute] = $this->dolzhService->find($value)->dolzh_name;
                    break;
                case 'podraz_id':
                    $diff[$attribute] = $this->podrazService->find($value)->podraz_name;
                    break;
            }
        }
    }

    protected function fireNotFoundPersons()
    {
        $persons = Person::find()
            ->andWhere(['for_import' => null, 'person_fired' => null])
            ->andWhere(['not', ['person_code' => 1]])
            ->orderBy(['person_fullname' => SORT_ASC, 'person_code' => SORT_ASC])
            ->all();

        /** @var Person $person */
        foreach ($persons as $person) {
            $this->changeNotFoundPersonFireDate($person);
        }

        return (bool)$persons;
    }

    protected function changeNotFoundPersonFireDate(Person $person)
    {
        $diffWas = ['person_fired' => $person->person_fired];
        $diff = ['person_fired' => date('Y-m-d')];

        $userFormUpdate = new UserFormUpdate($person, ['person_fired' => date('Y-m-d')]);
        $profile = $this->personService->getProfile($person->primaryKey);
        $profileForm = new ProfileForm($profile, []);
        $sourceString = "{$person->person_code}, {$person->person_fullname}";

        return $this->updateByService($this->personService, [$person->primaryKey, $userFormUpdate, $profileForm], $diff, $diffWas, $sourceString);
    }

    protected function closeNotFoundParttimes()
    {
        $parttimes = Parttime::find()
            ->andWhere(['for_import' => null, 'parttime_end' => null])
            ->all();

        /** @var Parttime $parttime */
        foreach ($parttimes as $parttime) {
            $this->changeNotFoundParttimeDateEnd($parttime);
        }

        return (bool)$parttimes;
    }

    protected function changeNotFoundParttimeDateEnd(Parttime $parttime)
    {
        $parttimeForm = $parttime->attributes;
        $parttimeForm['parttime_end'] = date('Y-m-d');

        $diff = array_diff_assoc($parttimeForm, $parttime->attributes);
        $diffWas = array_diff_assoc($parttime->attributes, $parttimeForm);

        if ($diff) {
            $parttimeCurrentForm = new ParttimeUpdateForm($parttime, $diff);
            $sourceMessage = "Совместительство '{$parttime->person->person_fullname}' от {$parttime->parttime_begin}";

            if ($this->updateByService($this->parttimeService, [$parttime->primaryKey, $parttimeCurrentForm], $diff, $diffWas, $sourceMessage) === false) {
                return false;
            };

            $this->reportStatus = self::REPORT_STATUS_CHANGED;
        }

        return true;
    }

    protected function resetForImportFields()
    {
        Person::updateAll(['for_import' => null], ['for_import' => 1]);
        Parttime::updateAll(['for_import' => null], ['for_import' => 1]);
    }

    protected function markPerson($person_id)
    {
        Person::updateAll(['for_import' => 1], ['person_id' => $person_id]);
    }

    protected function markParttime($parttime_id)
    {
        Parttime::updateAll(['for_import' => 1], ['parttime_id' => $parttime_id]);
    }
}