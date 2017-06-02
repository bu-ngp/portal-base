<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.06.2017
 * Time: 18:22
 */

namespace common\widgets\ActiveForm;


class ActiveFilterForm extends ActiveForm
{
    public $fieldClass = 'common\widgets\ActiveForm\ActiveField';

    public function field($model, $attribute, $options = [])
    {
       if (isset($options['options']['class'])) {
            $options['options']['class'] .= empty($model->$attribute) ? 'form-group' : 'form-group filter-marked';
        } else {
            $options['options']['class'] = empty($model->$attribute) ? 'form-group' : 'form-group filter-marked';
        }

        return parent::field($model, $attribute, $options);
    }
}