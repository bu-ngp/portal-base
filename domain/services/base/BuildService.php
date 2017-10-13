<?php

namespace domain\services\base;

use domain\forms\base\BuildForm;
use domain\models\base\Build;
use domain\repositories\base\BuildRepository;
use domain\services\WKService;

class BuildService extends WKService
{
    private $buildRepository;

    public function __construct(
        BuildRepository $buildRepository
    )
    {
        $this->buildRepository = $buildRepository;
    }

    public function find($id) {
        return $this->buildRepository->find($id);
    }

    public function create(BuildForm $form)
    {
        $build = Build::create($form->build_name);
        if (!$this->validateModels($build, $form)) {
            throw new \DomainException();
        }

        $this->buildRepository->add($build);
    }

    public function update($id, BuildForm $form)
    {
        $build = $this->buildRepository->find($id);
        $build->editData($form->build_name);
        if (!$this->validateModels($build, $form)) {
            throw new \DomainException();
        }

        $this->buildRepository->save($build);
    }

    public function delete($id)
    {
        $build = $this->buildRepository->find($id);
        $this->buildRepository->delete($build);
    }
}