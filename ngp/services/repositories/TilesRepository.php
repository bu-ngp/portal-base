<?php

namespace ngp\services\repositories;

use ngp\services\models\Tiles;
use domain\exceptions\ServiceErrorsException;
use RuntimeException;
use Yii;

class TilesRepository
{
    /**
     * @param $id
     * @return Tiles
     */
    public function find($id)
    {
        if (!$tiles = Tiles::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $tiles;
    }

    /**
     * @param Tiles $tiles
     */
    public function add($tiles)
    {
        if (!$tiles->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$tiles->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Tiles $tiles
     */
    public function save($tiles)
    {
        if ($tiles->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($tiles->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Tiles $tiles
     */
    public function delete($tiles)
    {
        if (!$tiles->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}