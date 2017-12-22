<?php

namespace domain\services\base;

use domain\forms\base\DolzhForm;
use domain\helpers\BinaryHelper;
use domain\models\base\Dolzh;
use domain\repositories\base\DolzhRepository;
use domain\services\Service;
use wartron\yii2uuid\helpers\Uuid;

class DolzhService extends Service
{
    private $dolzhs;

    public function __construct(
        DolzhRepository $dolzhs
    )
    {
        $this->dolzhs = $dolzhs;
    }

    public function find($id)
    {
        $uuid = BinaryHelper::isBinaryValidString($id) ? Uuid::str2uuid($id) : $id;
        return $this->dolzhs->find($uuid);
    }

    public function findIDByName($dolzh_name)
    {
        $dolzh = $this->dolzhs->findByName($dolzh_name);
        if ($dolzh) {
            return $dolzh->primaryKey;
        }

        return false;
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
        $dolzh = $this->find($id);
        $this->dolzhs->delete($dolzh);
    }
}