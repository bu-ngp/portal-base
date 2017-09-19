<?php
namespace common\models;

use common\models\base\Person;
use common\models\base\PersonLdap;
use domain\models\base\ConfigLdap;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
/*
            if (!$user) {
                $user = $this->getUserLdap();
            }*/

            if (!$user) {
                $this->addError($attribute, 'Incorrect username or password');
            }
/*
            if (!$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect password.');
            }*/
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
           // return Yii::$app->userLdap->login($this->_user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            return Yii::$app->user->login($this->_user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * @return null|Person
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Person::findByUsername($this->username, $this->password);
        }

        return $this->_user;
    }

    /**
     * @return null|PersonLdap
     */
    protected function getUserLdap()
    {
        if ($this->_user === null && ConfigLdap::findOne(1)->config_ldap_active) {
            $this->_user = PersonLdap::findByUsername($this->username, $this->password);
        }

        return $this->_user;
    }
}
