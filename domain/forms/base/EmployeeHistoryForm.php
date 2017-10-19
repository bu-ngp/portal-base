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
    public $build_id;
    public $employee_history_begin;
    public $created_at;
    public $updated_at;
    public $created_by;
    public $updated_by;

   // public $assignBuilds;

    public function __construct(EmployeeHistory $employee = null, $config = [])
    {
        if ($employee) {
            $this->load($employee->attributes, '');
        } else {
            $this->person_id = Yii::$app->request->get('person');
            $this->employee_history_begin = date('Y-m-d');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return array_merge(EmployeeHistoryRules::client(), [
           // [['assignBuilds'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return (new EmployeeHistory())->attributeLabels();
    }
}