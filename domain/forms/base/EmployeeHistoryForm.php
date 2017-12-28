<?php

namespace domain\forms\base;

use domain\models\base\EmployeeHistory;
use domain\rules\base\EmployeeHistoryRules;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class EmployeeHistoryForm extends Model
{
    public $person_id;
    public $dolzh_id;
    public $podraz_id;
    public $employee_history_begin;

    public $assignBuilds;

    public function __construct(EmployeeHistory $employee = null, $autoFill = true, $config = [])
    {
        if ($employee) {
//            $this->person_id = Uuid::uuid2str($employee->person_id);
//            $this->dolzh_id = Uuid::uuid2str($employee->dolzh_id);
//            $this->podraz_id = Uuid::uuid2str($employee->podraz_id);
            $this->person_id = $employee->person_id;
            $this->dolzh_id = $employee->dolzh_id;
            $this->podraz_id = $employee->podraz_id;
            $this->employee_history_begin = $employee->employee_history_begin;
        } elseif ($autoFill) {
            $this->person_id = Yii::$app->request->get('person');
            $this->employee_history_begin = date('Y-m-d');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return ArrayHelper::merge(EmployeeHistoryRules::client(), [
            [['assignBuilds'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return (new EmployeeHistory())->attributeLabels();
    }
}