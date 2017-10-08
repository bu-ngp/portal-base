<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 18:05
 */

namespace domain\validators;


use yii\validators\Validator;

class LoginValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $this->filterFIO($model, $attribute);

        if (!preg_match('/^[a-z][a-z-_0-9]+$/', $model->$attribute)) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\" может содержать только буквы на латинице, '-' и '_' и цифры. Первый символ должен быть буквой.");
            return;
        }
    }

    private function filterFIO($model, $attribute)
    {
        $model->$attribute = mb_strtolower($model->$attribute, 'UTF-8');
    }
}