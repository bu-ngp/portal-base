<?php

namespace domain\repositories\base;

use domain\models\base\Podraz;
use Yii;
use yii\db\ActiveRecord;

class PodrazRepository
{
    /**
     * @param $id
     * @return Podraz
     */
    public function find($id)
    {
        if (!$podraz = Podraz::findOne($id)) {
            throw new \RuntimeException('Model not found.');
        }

        return $podraz;
    }

    /**
     * @param $podraz_name
     * @return null|Podraz|ActiveRecord
     */
    public function findByName($podraz_name)
    {
        return Podraz::find()->andWhere(['like', 'podraz_name', trim($podraz_name), false])->one();
    }

    /**
     * @param Podraz $podraz
     */
    public function add(Podraz $podraz)
    {
        if (!$podraz->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$podraz->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Podraz $podraz
     */
    public function save(Podraz $podraz)
    {
        if ($podraz->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($podraz->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Podraz $podraz
     * @return bool
     */
    public function delete(Podraz $podraz)
    {
        try {
            return $podraz->delete() !== false;
        } catch (\Exception $e) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'), 0, $e);
        }
    }
}