<?php

namespace domain\services\base;

use domain\forms\base\EmployeeBuildForm;
use domain\forms\base\EmployeeForm;
use domain\forms\base\EmployeeHistoryForm;
use domain\models\base\Employee;
use domain\models\base\EmployeeHistory;
use domain\models\base\EmployeeHistoryBuild;
use domain\repositories\base\EmployeeHistoryBuildRepository;
use domain\repositories\base\EmployeeHistoryRepository;
use domain\repositories\base\EmployeeRepository;
use domain\services\WKService;
use Yii;

class EmployeeBuildService extends WKService
{
    private $employeeHistoryBuildRepository;

    public function __construct(
        EmployeeHistoryBuildRepository $employeeHistoryBuildRepository
    )
    {
        $this->employeeHistoryBuildRepository = $employeeHistoryBuildRepository;
    }

    public function get($id) {
        return $this->employeeHistoryBuildRepository->find($id);
    }

    public function create(EmployeeBuildForm $form)
    {
        $this->guardEmployeeExists($form);

        $employee = EmployeeHistoryBuild::create($form);
        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->employeeHistoryBuildRepository->add($employee);

        return $employee->primaryKey;
    }

    public function update($id, EmployeeBuildForm $form)
    {
        $employee = $this->employeeHistoryBuildRepository->find($id);

        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->employeeHistoryBuildRepository->save($employee);
    }

    public function delete($id)
    {
        $employee = $this->employeeHistoryBuildRepository->find($id);
        $this->employeeHistoryBuildRepository->delete($employee);
    }

    protected function guardEmployeeExists(EmployeeBuildForm $form)
    {
        if (!$form->employee_history_id) {
            throw new \DomainException(Yii::t('domain/employee', 'URL parameter "employee" is missed.'));
        }
    }
}