<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 11:17
 */

namespace domain\forms;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $person_username;
    public $person_password;
    public $person_rememberMe = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['person_username', 'person_password'], 'required'],
            // rememberMe must be a boolean value
            ['person_rememberMe', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'person_username' => Yii::t('common/person', 'Person Username'),
            'person_password' => Yii::t('common/person', 'Person Password'),
            'person_rememberMe' => Yii::t('common/person', 'Remember Me'),
        ];
    }
}