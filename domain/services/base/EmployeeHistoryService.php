<?php

namespace domain\services\base;

use domain\forms\base\EmployeeHistoryForm;
use domain\models\base\Employee;
use domain\models\base\EmployeeHistory;
use domain\models\base\EmployeeHistoryBuild;
use domain\repositories\base\EmployeeHistoryBuildRepository;
use domain\repositories\base\EmployeeHistoryRepository;
use domain\repositories\base\EmployeeRepository;
use domain\services\TransactionManager;
use domain\services\WKService;
use Yii;

class EmployeeHistoryService extends WKService
{
    private $transactionManager;
    private $employeeHistories;
    private $employees;
    private $employeeHistoryBuilds;

    public function __construct(
        TransactionManager $transactionManager,
        EmployeeHistoryRepository $employeeHistories,
        EmployeeRepository $employees,
        EmployeeHistoryBuildRepository $employeeHistoryBuilds
    )
    {
        $this->transactionManager = $transactionManager;
        $this->employeeHistories = $employeeHistories;
        $this->employees = $employees;
        $this->employeeHistoryBuilds = $employeeHistoryBuilds;
    }

    public function get($id)
    {
        return $this->employeeHistories->find($id);
    }

    public function create(EmployeeHistoryForm $form)
    {
        $this->guardPersonExists($form);
        $employeeHistory = EmployeeHistory::create($form);

        if (!$this->validateModels($employeeHistory, $form)) {
            throw new \DomainException();
        }

        /** @var Employee $employee */
        if ($employee = $this->employees->findByPerson($employeeHistory->person_id)) {
            $employee->edit($form);
        } else {
            $employee = Employee::create($form);
        }

        return $this->transactionManager->execute(function () use ($employeeHistory, $employee) {
            $this->employeeHistories->add($employeeHistory);
            $employee->isNewRecord ? $this->employees->add($employee) : $this->employees->save($employee);

            return $employeeHistory->primaryKey;
        });
    }

    public function update($id, EmployeeHistoryForm $form)
    {
        $employee = $this->employeeHistories->find($id);
        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->employeeHistories->save($employee);
    }

    public function delete($id)
    {
        $employeeHistory = $this->employeeHistories->find($id);
        $this->employeeHistories->delete($employeeHistory);
    }

    protected function guardPersonExists(EmployeeHistoryForm $form)
    {
        if (!$form->person_id) {
            throw new \DomainException(Yii::t('domain/employee', 'URL parameter "person" is missed.'));
        }
    }
}