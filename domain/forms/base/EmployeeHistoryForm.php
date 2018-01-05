<?php

namespace domain\forms\base;

use domain\models\base\EmployeeHistory;
use domain\rules\base\EmployeeHistoryRules;
use domain\validators\Str2UUIDValidator;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\Request;

class EmployeeHistoryForm extends Model
{
    public $person_id;
    public $dolzh_id;
    public $podraz_id;
    public $employee_history_begin;

    public $assignBuilds;

    public function __construct($config = [])
    {
        if (Yii::$app->request instanceof Request) {
            $this->person_id = Yii::$app->request->get('person');
        }
        $this->employee_history_begin = date('Y-m-d');

        parent::__construct($config);
    }

    public function rules()
    {
        return ArrayHelper::merge(EmployeeHistoryRules::client(), [
            [['!person_id'], 'required'],
            [['!person_id', 'dolzh_id', 'podraz_id'], Str2UUIDValidator::className()],
            [['assignBuilds'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return (new EmployeeHistory())->attributeLabels();
    }
}