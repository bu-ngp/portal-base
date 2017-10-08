<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 17:51
 */

namespace domain\validators;


use yii\validators\Validator;

class FIOValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $this->filterFIO($model, $attribute);

        if (!preg_match('/^(\b[а-я-]+\b)\s(\b[а-я-]+\b)(\s(\b[а-я-]+\b))?(\s(\b[а-я-]+\b))?$/iu', $model->$attribute)) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\" должны состоять минимум из двух слов только на кирилице");
            return;
        }

        if (preg_match('/-{2,}/u', $model->$attribute)) {
            $model->addError($attribute, "\"{$model->getAttributeLabel($attribute)}\" не может содержать два дифиса подряд");
            return;
        }
    }

    private function filterFIO($model, $attribute)
    {
        $model->$attribute = mb_strtoupper(trim($model->$attribute), 'UTF-8');
    }
}