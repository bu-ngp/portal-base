<?php

namespace domain\repositories\base;

use domain\models\base\Parttime;
use Yii;

class ParttimeRepository
{
    /**
     * @param $id
     * @return Parttime
     */
    public function find($id)
    {
        if (!$parttime = Parttime::findOne($id)) {
            throw new \RuntimeException('Model not found.');
        }

        return $parttime;
    }

    /**
     * @param Parttime $parttime
     */
    public function add(Parttime $parttime)
    {
        if (!$parttime->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$parttime->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Parttime $parttime
     */
    public function save(Parttime $parttime)
    {
        if ($parttime->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($parttime->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Parttime $parttime
     */
    public function delete($parttime)
    {
        if (!$parttime->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}