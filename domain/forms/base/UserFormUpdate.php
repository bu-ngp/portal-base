<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 14:29
 */

namespace domain\forms\base;


use common\classes\validators\WKDateValidator;
use common\models\base\Person;
use domain\models\base\EmployeeHistory;
use domain\rules\base\UserRules;
use yii\base\Model;

class UserFormUpdate extends Model
{
    public $person_fullname;
    public $person_username;
    public $person_email;
    public $person_hired;
    public $person_fired;

    public $hasActiveEmployee;

    public function __construct(Person $person, $config = [])
    {
        $this->person_fullname = $person->person_fullname;
        $this->person_username = $person->person_username;
        $this->person_email = $person->person_email;
        $this->person_hired = $person->person_hired;
        $this->person_fired = $person->person_fired;
        $this->hasActiveEmployee = EmployeeHistory::activeEmployees($person->person_id);

        parent::__construct($config);
    }

    public function rules()
    {
        return array_merge(UserRules::client(), [
            [['!person_hired', 'person_fired'], WKDateValidator::className()],
        ]);
    }

    public function attributeLabels()
    {
        return (new Person())->attributeLabels();
    }
}