<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 06.10.2017
 * Time: 13:22
 */

namespace common\classes\validators;


use yii\validators\DateValidator;

class WKDateValidator extends DateValidator
{
    public $format = 'yyyy-MM-dd';

    public function validateAttribute($model, $attribute)
    {
        $this->filterAttribute($model, $attribute);

        return parent::validateAttribute($model, $attribute);
    }

    protected function filterAttribute($model, $attribute)
    {
        $model->$attribute = preg_replace('/(\d{2}).(\d{2}).(\d{4})(\s(\d{2}):(\d{2}):(\d{2}))?/', '$3-$2-$1 $5:$6:$7', $model->$attribute);
    }
}