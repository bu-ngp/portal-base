<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 17:51
 */

namespace ngp\services\validators;


use yii\validators\Validator;

class PatientNameValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $this->filterName($model, $attribute);

        if (!preg_match('/^(\b[а-яё-]+\b)$/iu', $model->$attribute)) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\" должно состоять только на кирилице");
            return;
        }

        if (preg_match('/-{2,}/u', $model->$attribute)) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\" не может содержать два дифиса подряд");
            return;
        }
    }

    private function filterName($model, $attribute)
    {
        $model->$attribute = mb_strtoupper(trim($model->$attribute), 'UTF-8');
    }
}