<?php

namespace domain\services\base;

use domain\forms\base\EmployeeHistoryUpdateForm;
use domain\helpers\BinaryHelper;
use domain\forms\base\EmployeeHistoryForm;
use domain\models\base\Build;
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

    public function getCurrentEmployeeByPerson($person_id)
    {
        return $this->employeeHistories->findCurrentByPerson($person_id);
    }

    public function getEmployeeByDate($person_id, $date)
    {
        $result = $this->employeeHistories->findByDate($person_id, $date);
        if ($result === null) {
            return false;
        }

        return $result;
    }

    public function create(EmployeeHistoryForm $form)
    {
        $this->guardAssignBuilds($form);
        $employeeHistory = EmployeeHistory::create($form);

        if (!$this->validateModels($employeeHistory, $form)) {
            throw new \DomainException();
        }

        /** Если текущая дата специальности меньше дата приема на работу или это первая специальность,
         * то изменить дату приема на работу */
        $person = $this->changePersonHired($employeeHistory->person_id, $employeeHistory->employee_history_begin);

        /** Изменить текущую специальность или добавить, если ее не было */
        $employee = $this->currentEmployee($employeeHistory);

        $this->transactionManager->execute(function () use ($employeeHistory, $employee, $person) {
            if ($person) {
                $this->persons->save($person);
            }

            $this->employeeHistories->add($employeeHistory);
            $employee->isNewRecord ? $this->employees->add($employee) : $this->employees->save($employee);
        });
    }

    public function update($id, EmployeeHistoryUpdateForm $form)
    {
        $employeeHistory = $this->employeeHistories->find($id);
        $employeeHistory->edit($form);

        if (!$this->validateModels($employeeHistory, $form)) {
            throw new \DomainException();
        }

        $person = $this->changePersonHired($employeeHistory->person_id, $employeeHistory->employee_history_begin);

        /** Изменить текущую специальность или добавить, если ее не было */
        $employee = $this->currentEmployee($employeeHistory);

        $this->transactionManager->execute(function () use ($employeeHistory, $employee, $person) {
            if ($person) {
                $this->persons->save($person);
            }

            $this->employeeHistories->save($employeeHistory);
            $this->employees->save($employee);
        });
    }

    public function delete($id)
    {
        $employeeHistory = $this->employeeHistories->find($id);
        $this->guardParttimesExists($employeeHistory);
        $person = $this->updatePersonForDelete($employeeHistory);

        /** Изменить текущую специальность */
        $employee = $this->currentEmployeeForDelete($employeeHistory);

        $this->transactionManager->execute(function () use ($employeeHistory, $employee, $person) {
            if ($person) {
                $this->persons->save($person);
            }

            $this->employeeHistories->delete($employeeHistory);
            $this->employees->save($employee);
        });
    }

    protected function changePersonHired($person_id, $date)
    {
        $person = $this->persons->find($person_id);
        /** Если текущая дата специальности меньше дата приема на работу или это первая специальность,
         * то изменить дату приема на работу */
        if (strtotime($date) < strtotime($person->person_hired) || $person->person_hired === null) {
            $person->person_hired = $date;
            return $person;
        }
        return false;
    }

    protected function currentEmployee(EmployeeHistory $employeeHistory)
    {
        /** @var Employee $employee */
        /** Изменить текущую специальность или добавить, если ее не было */
        if ($employee = $this->employees->findByPerson($employeeHistory->person_id)) {
            if ($employee->employee_begin <= $employeeHistory->employee_history_begin) {
                $employee->edit($employeeHistory);
            }
        } else {
            $employee = Employee::create($employeeHistory);
        }

        return $employee;
    }

    protected function currentEmployeeForDelete(EmployeeHistory $employeeHistory)
    {
        /** @var Employee $employee */
        /** Изменить текущую специальность */
        $employee = $this->employees->findByPerson($employeeHistory->person_id);
        if ($employee->employee_begin === $employeeHistory->employee_history_begin) {
            $previousEmployee = $this->employeeHistories->previousBy($employeeHistory->employee_history_id, $employeeHistory->person_id);
            $employee->edit($previousEmployee);
        }

        return $employee;
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

        $form->assignBuilds = array_filter(array_map(function ($build_id) {
            $build_id = BinaryHelper::isBinaryValidString($build_id) ? Uuid::str2uuid($build_id) : $build_id;
            if (Build::findOne($build_id)) {
                return $build_id;
            }
            return false;
        }, $form->assignBuilds));
    }
}