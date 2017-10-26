<?php

namespace domain\services\base;

use common\widgets\GridView\services\GridViewHelper;
use domain\forms\base\EmployeeBuildForm;
use domain\models\base\EmployeeHistoryBuild;
use domain\repositories\base\EmployeeHistoryBuildRepository;
use domain\services\Service;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

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
        $this->guardEmployeeExists($form);
        $this->filterEmployeeUUID($form);
        $employee = EmployeeHistoryBuild::create($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->employeeHistoryBuilds->add($employee);
    }

    public function update($id, EmployeeBuildForm $form)
    {
        $employee = $this->employeeHistoryBuilds->find($id);
        $this->filterEmployeeUUID($form);
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

    protected function guardEmployeeExists(EmployeeBuildForm $form)
    {
        if (!$form->employee_history_id) {
            throw new \DomainException(Yii::t('domain/employee', 'URL parameter "employee" is missed.'));
        }
    }

    protected function filterEmployeeUUID(EmployeeBuildForm $form)
    {
        if (GridViewHelper::isBinaryValidString($form->build_id)) {
            $form->build_id = Uuid::str2uuid($form->build_id);
        } else {
            throw new \RuntimeException(Yii::t('domain/employee', 'Invalid UUID Parameters.'));
        }
    }
}