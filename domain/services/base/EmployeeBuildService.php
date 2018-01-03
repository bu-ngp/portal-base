<?php

namespace domain\services\base;

use domain\forms\base\EmployeeBuildUpdateForm;
use domain\forms\base\EmployeeBuildForm;
use domain\models\base\EmployeeHistoryBuild;
use domain\repositories\base\EmployeeHistoryBuildRepository;
use domain\services\Service;

class EmployeeBuildService extends Service
{
    private $employeeHistoryBuilds;

    public function __construct(
        EmployeeHistoryBuildRepository $employeeHistoryBuilds
    )
    {
        $this->employeeHistoryBuilds = $employeeHistoryBuilds;
    }

    public function get($id)
    {
        return $this->employeeHistoryBuilds->find($id);
    }

    public function create(EmployeeBuildForm $form)
    {
        $employee = EmployeeHistoryBuild::create($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->employeeHistoryBuilds->add($employee);
    }

    public function update($id, EmployeeBuildUpdateForm $form)
    {
        $employee = $this->employeeHistoryBuilds->find($id);
        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->employeeHistoryBuilds->save($employee);
    }

    public function delete($id)
    {
        $employee = $this->employeeHistoryBuilds->find($id);
        $this->employeeHistoryBuilds->delete($employee);
    }
}