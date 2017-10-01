<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.10.2017
 * Time: 9:06
 */

namespace domain\forms\base;

use common\models\base\Person;
use domain\models\base\Profile;
use yii\base\Model;

class UserForm extends Model
{
    public $person_fullname;
    public $person_username;
    public $person_password;
    public $person_password_repeat;
    public $person_email;
    public $person_fired;

    public $assignEmployees;
    public $assignRoles;

    public function rules()
    {
        return array_merge((new Person())->rules(), (new Profile())->rules());
    }

    public function attributeLabels()
    {
        return array_merge((new Person())->attributeLabels(), (new Profile())->attributeLabels());
    }
}