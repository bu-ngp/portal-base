<?php

namespace domain\repositories\base;

use domain\models\base\Build;
use Yii;

class BuildRepository
{
    /**
     * @param $id
     * @return Build
     */
    public function find($id)
    {
        if (!$build = Build::findOne($id)) {
            throw new \RuntimeException('Model not found.');
        }

        return $build;
    }

    /**
     * @param Build $build
     */
    public function add(Build $build)
    {
        if (!$build->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$build->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Build $build
     */
    public function save(Build $build)
    {
        if ($build->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($build->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Build $build
     * @return bool
     */
    public function delete(Build $build)
    {
        try {
            return $build->delete() !== false;
        } catch (\Exception $e) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'), 0, $e);
        }
    }
}