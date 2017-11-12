<?php

namespace ngp\services\services;

use ngp\services\models\Tiles;
use ngp\services\repositories\TilesRepository;
use domain\services\Service;
use ngp\services\forms\TilesForm;
use Yii;
use yii\web\UploadedFile;

class TilesService extends Service
{
    private $tiles;

    public function __construct(
        TilesRepository $tiles
    )
    {
        $this->tiles = $tiles;
    }

    public function find($id)
    {
        return $this->tiles->find($id);
    }

    public function create(TilesForm $form)
    {
        $tiles = Tiles::create($form);
        if (!$this->validateModels($tiles, $form)) {
            throw new \DomainException();
        }

        $this->tiles->add($tiles);
    }

    public function update($id, TilesForm $form)
    {
        $tiles = $this->tiles->find($id);
        $tiles->edit($form);
        if (!$this->validateModels($tiles, $form)) {
            throw new \DomainException();
        }

        $this->tiles->save($tiles);
    }

    public function delete($id)
    {
        $tiles = $this->tiles->find($id);
        $this->tiles->delete($tiles);
    }
}