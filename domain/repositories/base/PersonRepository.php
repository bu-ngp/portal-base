<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:21
 */

namespace domain\repositories\base;

use common\models\base\Person;
use Yii;

class PersonRepository
{
    /**
     * @param $id
     * @return Person
     */
    public function find($id)
    {
        if (!$person = Person::findOne($id)) {
            throw new \RuntimeException(Yii::t('domain/person', 'Person not found.'));
        }
        return $person;
    }

    /**
     * @param Person $person
     */
    public function add(Person $person)
    {
        if (!$person->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$person->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Person $person
     */
    public function save(Person $person)
    {
        if ($person->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($person->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param Person $person
     */
    public function delete(Person $person)
    {
        if (!$person->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}