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
        $thumbPath = $tiles->tiles_thumbnail;

        if (!$tiles->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }

        $this->removeOldThumbs($thumbPath);
    }

    protected function removeOldThumbs($pathThumb)
    {
        if ($pathThumb) {
            preg_match('/\/(\d+-)\d+x\d+(\.\w+)$/', $pathThumb, $matches);

            $resolutions = ['290x170', '145x85'];
            $path = Yii::getAlias('@thumbsPath');

            array_walk($resolutions, function ($resolution) use ($matches, $path) {
                if ($matches[1] && $matches[2]) {
                    if (file_exists($path . '/' . $matches[1] . $resolution . $matches[2])) {
                        unlink($path . '/' . $matches[1] . $resolution . $matches[2]);
                    }
                }
            });
        }
    }
}