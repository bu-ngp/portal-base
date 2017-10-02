<?php

namespace domain\forms\base;

use domain\models\base\EmployeeHistory;
use yii\base\Model;

class EmployeeHistoryForm extends Model
{
    public $person_id;
    public $dolzh_id;
    public $podraz_id;
    public $build_id;
    public $employee_history_begin;
    public $created_at;
    public $updated_at;
    public $created_by;
    public $updated_by;

    public function __construct(EmployeeHistory $employeeHistory = null, $config = [])
    {
        if ($employeeHistory) {
            $this->load($employeeHistory->attributes, '');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return (new EmployeeHistory())->rules();
    }

    public function attributeLabels()
    {
        return (new EmployeeHistory())->attributeLabels();
    }
}