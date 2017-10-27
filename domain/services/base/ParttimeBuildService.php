<?php

namespace domain\services\base;

use domain\helpers\BinaryHelper;
use domain\forms\base\ParttimeBuildForm;
use domain\models\base\ParttimeBuild;
use domain\repositories\base\ParttimeBuildRepository;
use domain\services\Service;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class ParttimeBuildService extends Service
{
    private $parttimeBuilds;

    public function __construct(
        ParttimeBuildRepository $parttimeBuilds
    )
    {
        $this->parttimeBuilds = $parttimeBuilds;
    }

    public function get($id) {
        return $this->parttimeBuilds->find($id);
    }

    public function create(ParttimeBuildForm $form)
    {
        $this->guardEmployeeExists($form);
        $this->filterEmployeeUUID($form);
        $employee = ParttimeBuild::create($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->parttimeBuilds->add($employee);
    }

    public function update($id, ParttimeBuildForm $form)
    {
        $employee = $this->parttimeBuilds->find($id);
        $this->filterEmployeeUUID($form);
        $employee->edit($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->parttimeBuilds->save($employee);
    }

    public function delete($id)
    {
        $employee = $this->parttimeBuilds->find($id);
        $this->parttimeBuilds->delete($employee);
    }

    protected function guardEmployeeExists(ParttimeBuildForm $form)
    {
        if (!$form->parttime_id) {
            throw new \DomainException(Yii::t('domain/parttime-build', 'URL parameter "employee" is missed.'));
        }
    }

    protected function filterEmployeeUUID(ParttimeBuildForm $form)
    {
        if (BinaryHelper::isBinaryValidString($form->build_id)) {
            $form->build_id = Uuid::str2uuid($form->build_id);
        } else {
            throw new \RuntimeException(Yii::t('domain/parttime-build', 'Invalid UUID Parameters.'));
        }
    }
}