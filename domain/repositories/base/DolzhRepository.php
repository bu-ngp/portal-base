<?php

namespace domain\repositories\base;

use domain\models\base\Dolzh;
use Yii;

class DolzhRepository
{
    /**
     * @param $id
     * @return Dolzh
     */
    public function find($id)
    {
        if (!$dolzh = Dolzh::findOne($id)) {
            throw new \RuntimeException('Model not found.');
        }

        return $dolzh;
    }

    /**
     * @param Dolzh $dolzh
     */
    public function add(Dolzh $dolzh)
    {
        if (!$dolzh->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$dolzh->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Dolzh $dolzh
     */
    public function save(Dolzh $dolzh)
    {
        if ($dolzh->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($dolzh->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Dolzh $dolzh
     */
    public function delete(Dolzh $dolzh)
    {
        if (!$dolzh->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}