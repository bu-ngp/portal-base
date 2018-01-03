<?php

namespace domain\services\base;

use domain\forms\base\ParttimeBuildUpdateForm;
use domain\forms\base\ParttimeBuildForm;
use domain\models\base\ParttimeBuild;
use domain\repositories\base\ParttimeBuildRepository;
use domain\services\Service;

class ParttimeBuildService extends Service
{
    private $parttimeBuilds;

    public function __construct(
        ParttimeBuildRepository $parttimeBuilds
    )
    {
        $this->parttimeBuilds = $parttimeBuilds;
    }

    public function get($id)
    {
        return $this->parttimeBuilds->find($id);
    }

    public function create(ParttimeBuildForm $form)
    {
        $employee = ParttimeBuild::create($form);

        if (!$this->validateModels($employee, $form)) {
            throw new \DomainException();
        }

        $this->parttimeBuilds->add($employee);
    }

    public function update($id, ParttimeBuildUpdateForm $form)
    {
        $employee = $this->parttimeBuilds->find($id);
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
}