<?php

namespace domain\repositories\base;

use domain\models\base\Podraz;
use domain\repositories\RepositoryInterface;
use RuntimeException;
use Yii;

class PodrazRepository implements RepositoryInterface
{
    /**
     * @param $id
     * @return Podraz
     */
    public function find($id)
    {
        if (!$podraz = Podraz::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $podraz;
    }

    /**
     * @param Podraz $podraz
     */
    public function add($podraz)
    {
        if (!$podraz->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$podraz->insert(false)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Podraz $podraz
     */
    public function save($podraz)
    {
        if ($podraz->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($podraz->update(false) === false) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Podraz $podraz
     */
    public function delete($podraz)
    {
        if (!$podraz->delete()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}