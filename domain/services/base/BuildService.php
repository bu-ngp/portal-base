<?php

namespace domain\services\base;

use domain\forms\base\BuildForm;
use domain\models\base\Build;
use domain\repositories\base\BuildRepository;
use domain\services\Service;

class BuildService extends Service
{
    private $builds;

    public function __construct(
        BuildRepository $builds
    )
    {
        $this->builds = $builds;
    }

    public function find($id)
    {
        return $this->builds->find($id);
    }

    public function create(BuildForm $form)
    {
        $build = Build::create($form);
        if (!$this->validateModels($build, $form)) {
            throw new \DomainException();
        }

        $this->builds->add($build);
    }

    public function update($id, BuildForm $form)
    {
        $build = $this->builds->find($id);
        $build->edit($form);
        if (!$this->validateModels($build, $form)) {
            throw new \DomainException();
        }

        $this->builds->save($build);
    }

    public function delete($id)
    {
        $build = $this->builds->find($id);
        $this->builds->delete($build);
    }
}