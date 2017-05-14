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

    public function login($person_username, $person_password, $rememberMe = true)
    {
        $person = $this->getPerson($person_username);
        if (!$person || !$person->validatePassword($person_password)) {
            throw new ServiceErrorsException('person_password', Yii::t('domain/base', 'Incorrect username or password.'));
        }

        return Yii::$app->user->login($person, $rememberMe ? 3600 * 24 * 30 : 0);
    }

    /**
     * Finds user by [[username]]
     *
     * @return Person|null
     */
    protected function getPerson($person_username)
    {
        if ($this->_person === null) {
            $this->_person = Person::findByUsername($person_username);
        }

        return $this->_person;
    }

    public function logout()
    {
        Yii::$app->user->logout();
    }
}