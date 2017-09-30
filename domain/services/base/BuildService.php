<?php

namespace domain\services\base;

use domain\models\base\Build;
use domain\repositories\base\BuildRepository;
use domain\services\BaseService;

class BuildService extends BaseService
{
    private $buildRepository;

    public function __construct(
        BuildRepository $buildRepository
    )
    {
        $this->buildRepository = $buildRepository;

        parent::__construct();
    }

    public function create($build_name)
    {
        $build = Build::create($build_name);
        $this->buildRepository->add($build);

        return true;
    }

    public function update($id, $build_name)
    {
        $build = $this->buildRepository->find($id);

        $build->editData($build_name);
        $this->buildRepository->save($build);

        return true;
    }

    public function delete($id)
    {
        $build = $this->buildRepository->find($id);
        $this->buildRepository->delete($build);
    }
}