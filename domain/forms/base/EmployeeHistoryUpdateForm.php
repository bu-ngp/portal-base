<?php

namespace domain\forms\base;

use domain\models\base\EmployeeHistory;
use domain\rules\base\EmployeeHistoryRules;
use domain\validators\Str2UUIDValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class EmployeeHistoryUpdateForm extends Model
{
    private $person_id;
    public $dolzh_id;
    public $podraz_id;
    public $employee_history_begin;

    public function __construct(EmployeeHistory $employee, $config = [])
    {
        $this->person_id = $employee->person_id;
        $this->dolzh_id = $employee->dolzh_id;
        $this->podraz_id = $employee->podraz_id;
        $this->employee_history_begin = $employee->employee_history_begin;

        parent::__construct($config);
    }

    /**
     * @return mixed
     */
    public function rules()
    {
        return ArrayHelper::merge(EmployeeHistoryRules::client(), [
            [['!person_id'], 'required'],
            [['!person_id', 'dolzh_id', 'podraz_id'], Str2UUIDValidator::className()],
        ]);
    }

    public function attributeLabels()
    {
        return (new EmployeeHistory())->attributeLabels();
    }

    public function getPerson_id()
    {
        return $this->person_id;
    }
}