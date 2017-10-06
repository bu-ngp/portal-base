<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 06.10.2017
 * Time: 13:39
 */

namespace common\classes\validators;


use Yii;
use yii\validators\Validator;

class SnilsValidator extends Validator
{
    static $LENGTH = 11;
    static $MIN_VALUE = 1001998;
    static $FACTOR = [9, 8, 7, 6, 5, 4, 3, 2, 1];

    public function validateAttribute($model, $attribute)
    {
        $this->filterSnils($model, $attribute);

        if (intval($model->{$attribute}) < self::$MIN_VALUE) {
            $model->addError($attribute, 'СНИЛС должен быть больше ' . static::$MIN_VALUE);
            return;
        }
        $length = strlen($model->{$attribute});
        if ($length != static::$LENGTH) {
            $model->addError($attribute, 'СНИЛС должен быть длинной ' . static::$LENGTH . ' символов');
            return;
        }
        if (substr($model->{$attribute}, -2, 2) != $this->getSnilsFactor($model->{$attribute})) {
            $model->addError($attribute, 'Неверная контрольная сумма СНИЛС');
            return;
        }
    }

    private function getSnilsFactor($value)
    {
        $sum = $this->calcSum($value);

        if ($sum < 100) {
            return $sum;
        }

        if ($sum == 100 || $sum == 101) {
            return 0;
        }

        return $sum % 101;
    }

    private function calcSum($value)
    {
        $sum = 0;
        $number = count(static::$FACTOR);
        for ($i = 0; $i != $number; $i++) {
            $sum += $value[$i] * static::$FACTOR[$i];
        }

        return $sum;
    }

    private function filterSnils($model, $attribute)
    {
        $model->$attribute = preg_replace('/[-\s]/', '', $model->$attribute);
    }
}