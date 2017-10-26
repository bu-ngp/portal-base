<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 24.10.2017
 * Time: 14:04
 */

namespace domain\validators;


use domain\models\base\EmployeeHistory;
use domain\models\base\Parttime;
use yii\validators\Validator;

class PersonFiredValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!EmployeeHistory::employeeExists($model->person_id)) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\": Отсутствуют специальности");
        }

        if ($employee = EmployeeHistory::denyAccessForDateFired($model->person_id, $model->$attribute)) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\" меньше даты специальности '{$employee->dolzh->dolzh_name}' от " . \Yii::$app->formatter->asDate($employee->employee_history_begin));
        }

        if ($parttime = Parttime::denyAccessForDateFired($model->person_id, $model->$attribute)) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\" меньше даты совмещения '{$employee->dolzh->dolzh_name}' от " . \Yii::$app->formatter->asDate($parttime->parttime_begin));
        }
    }
}