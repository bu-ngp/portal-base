<?php

namespace domain\forms\base;

use domain\models\base\EmployeeHistory;
use domain\rules\base\EmployeeHistoryRules;
use Yii;
use yii\base\Model;

class EmployeeHistoryForm extends Model
{
    public $person_id;
    public $dolzh_id;
    public $podraz_id;
    public $employee_history_begin;

    public function __construct(EmployeeHistory $employee = null, $config = [])
    {
        if ($employee) {
            $this->person_id = $employee->person_id;
            $this->dolzh_id = $employee->dolzh_id;
            $this->podraz_id = $employee->podraz_id;
            $this->employee_history_begin = $employee->employee_history_begin;
        } else {
            $this->person_id = Yii::$app->request->get('person');
            $this->employee_history_begin = date('Y-m-d');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return EmployeeHistoryRules::client();
    }

    public function attributeLabels()
    {
        return (new EmployeeHistory())->attributeLabels();
    }
}