<?php

namespace domain\services\base;

use domain\forms\base\DolzhForm;
use domain\models\base\Dolzh;
use domain\repositories\base\DolzhRepository;
use domain\services\WKService;

class DolzhService extends WKService
{
    private $dolzhRepository;

    public function __construct(
        DolzhRepository $dolzhRepository
    )
    {
        $this->dolzhRepository = $dolzhRepository;
    }

    public function create(DolzhForm $form)
    {
        $dolzh = Dolzh::create($form->dolzh_name);
        if (!$this->validateModels($dolzh, $form)) {
            return false;
        }

        return $this->dolzhRepository->add($dolzh);
    }

    public function update($id, DolzhForm $form)
    {
        $dolzh = $this->dolzhRepository->find($id);
        $dolzh->editData($form->dolzh_name);
        if (!$this->validateModels($dolzh, $form)) {
            return false;
        }

        return $this->dolzhRepository->save($dolzh);
    }

    public function delete($id)
    {
        $dolzh = $this->dolzhRepository->find($id);
        $this->dolzhRepository->delete($dolzh);
    }
}