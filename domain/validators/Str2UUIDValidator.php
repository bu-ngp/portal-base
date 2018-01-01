<?php

namespace domain\validators;


use domain\helpers\BinaryHelper;
use wartron\yii2uuid\helpers\Uuid;
use yii\validators\Validator;

class Str2UUIDValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (BinaryHelper::isBinary($model->$attribute)) {
            return;
        }

        if (BinaryHelper::isBinaryValidString($model->$attribute)) {
            $this->filterStr2Binary($model, $attribute);
            return;
        }

        $model->addError($attribute, "Не валидная UUID строка");
    }

    private function filterStr2Binary($model, $attribute)
    {
        $model->$attribute = Uuid::str2uuid($model->$attribute);
    }
}