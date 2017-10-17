<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 14:29
 */

namespace domain\forms\base;


use common\models\base\Person;
use domain\models\base\Profile;
use domain\rules\base\UserRules;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class UserFormUpdate extends Model
{
    public $person_fullname;
    public $person_username;
    public $person_email;
    public $person_fired;

    public $assignEmployees;
    public $assignRoles;

    public function __construct(Person $person = null, $config = [])
    {
        $this->person_fullname = $person->person_fullname;
        $this->person_username = $person->person_username;
        $this->person_email = $person->person_email;
        $this->person_fired = $person->person_fired;

        parent::__construct($config);
    }

    public function rules()
    {
        return UserRules::client();
    }

    public function attributeLabels()
    {
        return array_merge((new Person())->attributeLabels(), (new Profile())->attributeLabels(), [
            'person_password' => Yii::t('domain/person', 'Person Password'),
            'person_password_repeat' => Yii::t('domain/person', 'Person Password Repeat'),
        ]);
    }

}