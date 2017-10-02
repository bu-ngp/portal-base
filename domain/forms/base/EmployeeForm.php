<?php

namespace domain\forms\base;

use domain\models\base\Employee;
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
        if ($employee) {
            $this->load($employee->attributes, '');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return (new Employee())->rules();
    }

    public function attributeLabels()
    {
        return (new Employee())->attributeLabels();
    }
}