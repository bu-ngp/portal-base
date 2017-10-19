<?php

namespace domain\services\base;

use domain\forms\base\EmployeeBuildForm;
use domain\forms\base\EmployeeForm;
use domain\forms\base\ParttimeBuildForm;
use domain\forms\base\ParttimeForm;
use domain\models\base\Employee;
use domain\models\base\Parttime;
use domain\models\base\ParttimeBuild;
use domain\repositories\base\ParttimeBuildRepository;
use domain\repositories\base\ParttimeRepository;
use domain\repositories\base\EmployeeRepository;
use domain\services\WKService;
use Yii;

class ParttimeBuildService extends WKService
{
    private $parttimeBuildRepository;

    public function __construct(
        ParttimeBuildRepository $parttimeBuildRepository
    )
    {
        $this->parttimeBuildRepository = $parttimeBuildRepository;
    }

    public function get($id) {
        return $this->parttimeBuildRepository->find($id);
    }

    public function create(ParttimeBuildForm $form)
    {
        $this->guardEmployeeExists($form);

        $employee = ParttimeBuild::create($form);
        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->parttimeBuildRepository->add($employee);

        return $employee->primaryKey;
    }

    public function update($id, ParttimeBuildForm $form)
    {
        $employee = $this->parttimeBuildRepository->find($id);

        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->parttimeBuildRepository->save($employee);
    }

    public function delete($id)
    {
        $employee = $this->parttimeBuildRepository->find($id);
        $this->parttimeBuildRepository->delete($employee);
    }

    protected function guardEmployeeExists(ParttimeBuildForm $form)
    {
        if (!$form->parttime_id) {
            throw new \DomainException(Yii::t('domain/employee', 'URL parameter "employee" is missed.'));
        }
    }
}