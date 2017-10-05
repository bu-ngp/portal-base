<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.06.2017
 * Time: 18:22
 */

namespace common\widgets\ActiveForm;


class ActiveForm extends \yii\bootstrap\ActiveForm
{
    public $fieldClass = 'common\widgets\ActiveForm\ActiveField';

    /**
     * @inheritdoc
     * @return ActiveField the created ActiveField object
     */
    public function field($model, $attribute, $options = [])
    {
        return parent::field($model, $attribute, $options);
    }
}