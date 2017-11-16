<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 06.10.2017
 * Time: 13:22
 */

namespace ngp\services\validators;


use yii\validators\DateValidator;

class DRValidator extends DateValidator
{
    public $format = 'yyyy-MM-dd';

    public function validateAttribute($model, $attribute)
    {
        $this->filterAttribute($model, $attribute);
        parent::validateAttribute($model, $attribute);
    }

    protected function filterAttribute($model, $attribute)
    {
        $model->$attribute = \PHPExcel_Style_NumberFormat::toFormattedString($model->$attribute, 'YYYY-MM-DD');

        if (preg_match('/(\d{2}).(\d{2}).(\d{4})/', $model->$attribute)) {
            $model->$attribute = preg_replace('/(\d{2}).(\d{2}).(\d{4})/', '$3-$2-$1', $model->$attribute);
            return;
        }

        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $model->$attribute)) {
            return;
        }

        $this->addError($model, $attribute, 'Не соответствует формату даты');
    }
}