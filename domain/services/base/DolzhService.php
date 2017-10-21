<?php

namespace domain\services\base;

use domain\forms\base\DolzhForm;
use domain\models\base\Dolzh;
use domain\repositories\base\DolzhRepository;
use domain\services\WKService;

class DolzhService extends WKService
{
    private $dolzhs;

    public function __construct(
        DolzhRepository $dolzhs
    )
    {
        $this->dolzhs = $dolzhs;
    }

    public function find($id) {
        return $this->dolzhs->find($id);
    }

    public function create(DolzhForm $form)
    {
        $dolzh = Dolzh::create($form);
        if (!$this->validateModels($dolzh, $form)) {
            throw new \DomainException();
        }

        $this->dolzhs->add($dolzh);
    }

    public function update($id, DolzhForm $form)
    {
        $dolzh = $this->dolzhs->find($id);
        $dolzh->edit($form);
        if (!$this->validateModels($dolzh, $form)) {
            throw new \DomainException();
        }

        $this->dolzhs->save($dolzh);
    }

    public function delete($id)
    {
        $dolzh = $this->dolzhs->find($id);
        $this->dolzhs->delete($dolzh);
    }
}