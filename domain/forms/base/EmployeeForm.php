<?php

namespace domain\forms\base;

use domain\models\base\Employee;
use domain\rules\base\EmployeeRules;
use Yii;
use yii\base\Model;

class EmployeeForm extends Model
{
    public $person_id;
    public $dolzh_id;
    public $podraz_id;
    public $build_id;
    public $employee_begin;
    public $created_at;
    public $updated_at;
    public $created_by;
    public $updated_by;

    public function __construct(Employee $employee = null, $config = [])
    {
        $this->person_id = Yii::$app->request->get('person_id');
        $this->employee_begin = date('Y-m-d');

        if ($employee) {
            $this->load($employee->attributes, '');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return EmployeeRules::client();
    }

    public function attributeLabels()
    {
        return (new Employee())->attributeLabels();
    }
}