<?php

namespace domain\services\base;

use domain\helpers\BinaryHelper;
use domain\forms\base\EmployeeHistoryForm;
use domain\models\base\Employee;
use domain\models\base\EmployeeHistory;
use domain\repositories\base\EmployeeHistoryRepository;
use domain\repositories\base\EmployeeRepository;
use domain\repositories\base\ParttimeRepository;
use domain\repositories\base\PersonRepository;
use domain\services\TransactionManager;
use domain\services\Service;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class EmployeeHistoryService extends Service
{
    private $transactionManager;
    private $employeeHistories;
    private $employees;
    private $persons;
    private $parttimes;

    public function __construct(
        TransactionManager $transactionManager,
        EmployeeHistoryRepository $employeeHistories,
        EmployeeRepository $employees,
        PersonRepository $persons,
        ParttimeRepository $parttimes
    )
    {
        $this->transactionManager = $transactionManager;
        $this->employeeHistories = $employeeHistories;
        $this->employees = $employees;
        $this->persons = $persons;
        $this->parttimes = $parttimes;
    }

    public function get($id)
    {
        return $this->employeeHistories->find($id);
    }

    public function create(EmployeeHistoryForm $form)
    {
        $this->guardPersonExists($form);
        $this->guardAssignBuilds($form);
        $this->filterEmployeeUUIDCreate($form);
        $employeeHistory = EmployeeHistory::create($form);

        if (!$this->validateModels($employeeHistory, $form)) {
            throw new \DomainException();
        }

        $person = $this->changePersonHired($employeeHistory->person_id, $employeeHistory->employee_history_begin);

        /** @var Employee $employee */
        if ($employee = $this->employees->findByPerson($employeeHistory->person_id)) {
            $employee->edit($form);
        } else {
            $employee = Employee::create($form);
        }

        $this->transactionManager->execute(function () use ($employeeHistory, $employee, $person) {
            if ($person) {
                $this->persons->save($person);
            }

            $this->employeeHistories->add($employeeHistory);
            $employee->isNewRecord ? $this->employees->add($employee) : $this->employees->save($employee);
        });
    }

    public function update($id, EmployeeHistoryForm $form)
    {
        $employee = $this->employeeHistories->find($id);
        $this->filterEmployeeUUIDUpdate($form);
        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $person = $this->changePersonHired($employee->person_id, $employee->employee_history_begin);

        $this->transactionManager->execute(function () use ($employee, $person) {
            if ($person) {
                $this->persons->save($person);
            }

            $this->employeeHistories->save($employee);
        });
    }

    public function delete($id)
    {
        $employeeHistory = $this->employeeHistories->find($id);
        $this->guardParttimesExists($employeeHistory);
        $person = $this->updatePersonForDelete($employeeHistory);

        $this->transactionManager->execute(function () use ($employeeHistory, $person) {
            if ($person) {
                $this->persons->save($person);
            }

            $this->employeeHistories->delete($employeeHistory);
        });
    }

    protected function guardPersonExists(EmployeeHistoryForm $form)
    {
        if (!$form->person_id) {
            throw new \DomainException(Yii::t('domain/employee', 'URL parameter "person" is missed.'));
        }
    }

    protected function filterEmployeeUUIDCreate(EmployeeHistoryForm $form)
    {
        if (BinaryHelper::isBinaryValidString($form->person_id)
            && BinaryHelper::isBinaryValidString($form->dolzh_id)
            && BinaryHelper::isBinaryValidString($form->podraz_id)
        ) {
            $form->person_id = Uuid::str2uuid($form->person_id);
            $form->dolzh_id = Uuid::str2uuid($form->dolzh_id);
            $form->podraz_id = Uuid::str2uuid($form->podraz_id);
        } else {
            throw new \RuntimeException(Yii::t('domain/employee', 'Invalid UUID Parameters.'));
        }

        $form->assignBuilds = array_map(function ($buildId) {
            return BinaryHelper::isBinaryValidString($buildId) ? Uuid::str2uuid($buildId) : $buildId;
        }, $form->assignBuilds);
    }

    protected function filterEmployeeUUIDUpdate(EmployeeHistoryForm $form)
    {
        if (BinaryHelper::isBinaryValidString($form->dolzh_id)
            && BinaryHelper::isBinaryValidString($form->podraz_id)
        ) {
            $form->dolzh_id = Uuid::str2uuid($form->dolzh_id);
            $form->podraz_id = Uuid::str2uuid($form->podraz_id);
        } else {
            throw new \RuntimeException(Yii::t('domain/employee', 'Invalid UUID Parameters.'));
        }
    }

    protected function changePersonHired($person_id, $date)
    {
        $person = $this->persons->find($person_id);
        if (strtotime($date) < strtotime($person->person_hired) || $person->person_hired === null) {
            $before = $person->getAttributes();
            $person->person_hired = $date;
            $after = $person->getAttributes();
            $diff = array_diff_assoc($after, $before);
            if (isset($diff['person_hired'])) {
                return $person;
            }
        }
        return false;
    }

    protected function updatePersonForDelete(EmployeeHistory $employee)
    {
        $person = $this->persons->find($employee->person_id);

        if ($person->person_hired == $employee->employee_history_begin) {
            $previousEmployee = $this->employeeHistories->previousBy($employee->employee_history_id, $employee->person_id);
            $person->person_hired = $previousEmployee ? $previousEmployee->employee_history_begin : null;
        }

        if (!$this->employeeHistories->exists($employee->employee_history_id, $employee->person_id)) {
            $person->person_fired = null;
        }

        return $person;
    }

    protected function guardParttimesExists(EmployeeHistory $employeeHistory)
    {
        if ($this->parttimes->exists($employeeHistory->person_id)) {
            throw  new \Exception(Yii::t('domain/employee', 'You need remove Parttimes'));
        }
    }

    protected function guardAssignBuilds(EmployeeHistoryForm $form)
    {
        if (!is_string($form->assignBuilds) || ($form->assignBuilds = json_decode($form->assignBuilds)) === null) {
            throw new \DomainException(Yii::t('domain/base', 'Error when recognizing selected items'));
        }
    }
}