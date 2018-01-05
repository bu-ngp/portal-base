<?php

namespace domain\repositories\base;

use domain\models\base\Parttime;
use domain\models\base\Person;
use domain\models\base\Podraz;
use Yii;
use yii\db\ActiveRecord;

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
     * @param $person_id
     * @param $date
     * @return Parttime|null|ActiveRecord
     */
    public function findByDate($person_id, $date)
    {
        return Parttime::findOne(['person_id' => $person_id, 'parttime_begin' => $date]);
    }

    /**
     * @param $person_id
     * @param $dolzh_id
     * @param $podraz_id
     * @param $dateBegin
     * @return Parttime|false|ActiveRecord
     */
    public function findByAttributes($person_id, $dolzh_id, $podraz_id, $dateBegin)
    {
        $parttime = Parttime::findOne([
            'person_id' => $person_id,
            'dolzh_id' => $dolzh_id,
            'podraz_id' => $podraz_id,
            'parttime_begin' => $dateBegin,
        ]);

        return $parttime ?: false;
    }

    /**
     * @param Parttime $parttime
     * @return mixed
     */
    public function add(Parttime $parttime)
    {
        if (!$parttime->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$parttime->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
        return $parttime->primaryKey;
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

    public function getOpenAndMoreParttimes($person_id, $date_fire)
    {
        return Parttime::find()
            ->andWhere(['person_id' => $person_id])
            ->andWhere([
                'or',
                ['parttime_end' => null],
                ['>', 'parttime_end', $date_fire]
            ])
            ->all();
    }

    public function exists($person_id)
    {
        return Parttime::find()
            ->andWhere(['person_id' => $person_id])
            ->exists();
    }
}