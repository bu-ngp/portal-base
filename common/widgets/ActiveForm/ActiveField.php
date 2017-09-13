<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.06.2017
 * Time: 18:25
 */

namespace common\widgets\ActiveForm;


class ActiveField extends \yii\bootstrap\ActiveField
{
    public $checkboxTemplate = "<div class=\"checkbox pmd-default-theme\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";

    public function checkbox($options = [], $enclosedByLabel = true)
    {
        $model = $this->model;
        $attribute = $this->attribute;

        $options = empty($options) ? [
            'label' => "<span class=\"control-label\">{$model->getAttributeLabel($attribute)}</span>",
            'labelOptions' => ['class' => 'pmd-checkbox'],
        ] : array_replace([
            'label' => "<span class=\"control-label\">{$model->getAttributeLabel($attribute)}</span>",
            'labelOptions' => ['class' => 'pmd-checkbox'],
        ], $options);

        return parent::checkbox($options, $enclosedByLabel);
    }

    public function textInput($options = [])
    {
        $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield pmd-textfield-floating-label' : 'form-group pmd-textfield pmd-textfield-floating-label';

        return parent::textInput($options);
    }
}