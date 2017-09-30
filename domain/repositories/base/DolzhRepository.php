<?php

namespace domain\repositories\base;

use domain\models\base\Dolzh;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;

class DolzhRepository implements RepositoryInterface
{
    /**
     * @param $id
     * @return Dolzh
     */
    public function find($id)
    {
        if (!$dolzh = Dolzh::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $dolzh;
    }

    /**
     * @param Dolzh $dolzh
     */
    public function add($dolzh)
    {
        if (!$dolzh->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$dolzh->insert(false)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Dolzh $dolzh
     */
    public function save($dolzh)
    {
        if ($dolzh->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($dolzh->update(false) === false) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Dolzh $dolzh
     */
    public function delete($dolzh)
    {
        if (!$dolzh->delete()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}