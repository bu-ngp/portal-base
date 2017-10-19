<?php

namespace domain\repositories\base;

use domain\models\base\ParttimeBuild;
use RuntimeException;
use Yii;

class ParttimeBuildRepository
{
    /**
     * @param $id
     * @return ParttimeBuild
     */
    public function find($id)
    {
        if (!$parttimeBuild = ParttimeBuild::findOne($id)) {
            throw new \RuntimeException('Model not found.');
        }

        return $parttimeBuild;
    }

    /**
     * @param ParttimeBuild $parttimeBuild
     */
    public function add(ParttimeBuild $parttimeBuild)
    {
        if (!$parttimeBuild->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$parttimeBuild->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param ParttimeBuild $parttimeBuild
     */
    public function save(ParttimeBuild $parttimeBuild)
    {
        if ($parttimeBuild->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($parttimeBuild->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param ParttimeBuild $parttimeBuild
     */
    public function delete(ParttimeBuild $parttimeBuild)
    {
        if (!$parttimeBuild->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}