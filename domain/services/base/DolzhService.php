<?php

namespace domain\services\base;

use domain\models\base\Dolzh;
use domain\repositories\base\DolzhRepository;
use domain\services\BaseService;

class DolzhService extends BaseService
{
    private $dolzhRepository;

    public function __construct(
        DolzhRepository $dolzhRepository
    )
    {
        $this->dolzhRepository = $dolzhRepository;

        parent::__construct();
    }

    public function create($dolzh_name)
    {
        $dolzh = Dolzh::create($dolzh_name);
        $this->dolzhRepository->add($dolzh);

        return true;
    }

    public function update($id, $dolzh_name)
    {
        $dolzh = $this->dolzhRepository->find($id);

        $dolzh->editData($dolzh_name);
        $this->dolzhRepository->save($dolzh);

        return true;
    }

    public function delete($id)
    {
        $dolzh = $this->dolzhRepository->find($id);
        $this->dolzhRepository->delete($dolzh);
    }
}