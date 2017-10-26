<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.10.2017
 * Time: 9:06
 */

namespace domain\forms\base;

use domain\models\base\Person;
use domain\models\base\Profile;
use domain\rules\base\UserRules;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class UserForm extends Model
{
    public $person_fullname;
    public $person_username;
    public $person_password;
    public $person_password_repeat;
    public $person_email;

    public $assignRoles;

    public function rules()
    {
        return ArrayHelper::merge(UserRules::client(), [
            [['person_password', 'person_password_repeat'], 'required'],
            [['person_password'], 'string', 'min' => 6],
            [['person_password_repeat'], 'compare', 'compareAttribute' => 'person_password'],
            [['assignRoles'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge((new Person())->attributeLabels(), (new Profile())->attributeLabels(), [
            'person_password' => Yii::t('domain/person', 'Person Password'),
            'person_password_repeat' => Yii::t('domain/person', 'Person Password Repeat'),
        ]);
    }
}