<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 14:29
 */

namespace domain\forms\base;


use common\models\base\Person;
use domain\rules\base\UserRules;
use yii\base\Model;

class UserFormUpdate extends Model
{
    public $person_fullname;
    public $person_username;
    public $person_email;

    public function __construct(Person $person, $config = [])
    {
        $this->person_fullname = $person->person_fullname;
        $this->person_username = $person->person_username;
        $this->person_email = $person->person_email;

        parent::__construct($config);
    }

    public function rules()
    {
        return UserRules::client();
    }

    public function attributeLabels()
    {
        return (new Person())->attributeLabels();
    }
}