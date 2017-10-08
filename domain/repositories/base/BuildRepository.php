<?php

namespace domain\repositories\base;

use domain\models\base\Build;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;

class BuildRepository implements RepositoryInterface
{
    /**
     * @param $id
     * @return Build
     */
    public function find($id)
    {
        if (!$build = Build::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $build;
    }

    /**
     * @param Build $build
     * @return bool
     */
    public function add($build)
    {
        if (!$build->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$build->insert(false)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
        
        return true;
    }

    /**
     * @param Build $build
     * @return bool
     */
    public function save($build)
    {
        if ($build->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($build->update(false) === false) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }

        return true;
    }

    /**
     * @param Build $build
     */
    public function delete($build)
    {
        if (!$build->delete()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}