<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:21
 */

namespace domain\repositories\base;

use common\models\base\Person;
use domain\exceptions\ServiceErrorsException;
use domain\models\base\Profile;
use domain\repositories\RepositoryInterface;
use Yii;

class PersonRepository implements RepositoryInterface
{
    private $_person;

    public function find($id)
    {
        if (!$person = Person::findOne($id)) {
            throw new \RuntimeException(Yii::t('domain/person', 'Person not found.'));
        }
        return $person;
    }

    /**
     * @param $person Person
     */
    public function add($person)
    {
        if (!$person->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$person->insert(false)) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param $person Person
     */
    public function save($person)
    {
        if ($person->getIsNewRecord()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($person->update(false) === false) {
            throw new \RuntimeException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param $person Person
     */
    public function delete($person)
    {
        if (!$person->delete()) {
            throw new \RuntimeException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}