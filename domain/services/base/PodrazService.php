<?php

namespace domain\services\base;

use domain\forms\base\PodrazForm;
use domain\helpers\BinaryHelper;
use domain\models\base\Podraz;
use domain\repositories\base\PodrazRepository;
use domain\services\Service;
use wartron\yii2uuid\helpers\Uuid;

class PodrazService extends Service
{
    private $podrazs;

    public function __construct(
        PodrazRepository $podrazs
    )
    {
        $this->podrazs = $podrazs;
    }

    public function find($id)
    {
        $uuid = BinaryHelper::isBinaryValidString($id) ? Uuid::str2uuid($id) : $id;
        return $this->podrazs->find($uuid);
    }

    public function create(PodrazForm $form)
    {
        $podraz = Podraz::create($form);
        if (!$this->validateModels($podraz, $form)) {
            throw new \DomainException();
        }

        $this->podrazs->add($podraz);
    }

    public function update($id, PodrazForm $form)
    {
        $podraz = $this->podrazs->find($id);
        $podraz->edit($form);
        if (!$this->validateModels($podraz, $form)) {
            throw new \DomainException();
        }

        $this->podrazs->save($podraz);
    }

    public function delete($id)
    {
        $podraz = $this->podrazs->find($id);
        $this->podrazs->delete($podraz);
    }
}