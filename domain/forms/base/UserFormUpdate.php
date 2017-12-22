<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 14:29
 */

namespace domain\forms\base;


use domain\validators\FIOValidator;
use domain\validators\WKDateValidator;
use domain\models\base\Person;
use domain\models\base\EmployeeHistory;
use domain\rules\base\UserRules;
use yii\base\Model;

class UserFormUpdate extends Model
{
    public $person_code;
    public $person_fullname;
    public $person_username;
    public $person_email;
    public $person_hired;
    public $person_fired;

    public $hasActiveEmployee;

    public function __construct(Person $person, $config = [])
    {
        $this->person_code = $person->person_code;
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
            [['person_fullname'], FIOValidator::className(), 'when' => function ($model) {
                return $model->person_code !== 1;
            }],
            [['!person_hired', 'person_fired'], WKDateValidator::className()],
        ]);
    }

    public function attributeLabels()
    {
        return (new Person())->attributeLabels();
    }
}