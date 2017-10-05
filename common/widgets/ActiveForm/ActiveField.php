<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.06.2017
 * Time: 18:25
 */

namespace common\widgets\ActiveForm;


use common\widgets\PropellerAssets\DateTimePickerAsset;
use common\widgets\PropellerAssets\RadioAsset;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\validators\DateValidator;
use yii\widgets\MaskedInput;

class ActiveField extends \yii\bootstrap\ActiveField
{
    public $checkboxTemplate = "<div class=\"checkbox pmd-default-theme\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";
    public $inlineRadioListTemplate = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";


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
        $this->initWKIcon($options);
        $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield pmd-textfield-floating-label' : 'form-group pmd-textfield pmd-textfield-floating-label';

        return parent::textInput($options);
    }

    public function passwordInput($options = [])
    {
        $this->initWKIcon($options);
        $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield pmd-textfield-floating-label' : 'form-group pmd-textfield pmd-textfield-floating-label';

        return parent::passwordInput($options);
    }

    public function radioList($items, $options = [])
    {
        $options['itemOptions'] = [
            'labelOptions' => ['class' => 'radio-inline pmd-radio'],
        ];

        $this->options['class'] = 'form-group pmd-textfield';

        if (isset($options['wkkeep'])) {
            $options['itemOptions']['wkkeep'] = true;
            unset($options['wkkeep']);
        }

        if (isset($options['titles'])) {
            $options['item'] = function ($index, $label, $name, $checked, $value) use ($options) {
                $checked = $checked ? 'checked' : '';
                $wkkeep = $options['itemOptions']['wkkeep'] ? 'wkkeep' : '';

                return '<label class="radio-inline pmd-radio" title="' . $options['titles'][$value] . '"><input type="radio" name="' . $name . '" value="' . $value . '" ' . $checked . ' ' . $wkkeep . '>' . $label . '</label>';
            };

            unset($options['titles']);
        }

        RadioAsset::register($this->form->getView());

        parent::radioList($items, $options);

        return $this;
    }

    public function datetime($options = [])
    {
        $idInput = strtolower($this->model->formName()) . '-' . $this->attribute;

        $view = $this->form->getView();

        $format = 'DD.MM.YYYY';
        foreach ($this->model->getActiveValidators($this->attribute) as $validator) {
            if ($validator instanceof DateValidator) {
                if ($validator->format === 'yyyy-MM-dd') {
                    $format = 'DD.MM.YYYY';
                }

                if ($validator->format === 'yyyy-MM-dd HH:mm:ss') {
                    $format = 'DD.MM.YYYY HH:mm:ss';
                }
            }
        }

        $this->convertDateTimeValue($this->model, $this->attribute);

        $JSOptions = Json::encode([
            'locale' => 'ru',
            'format' => $format,
        ]);

        DateTimePickerAsset::register($view);
        $view->registerJs("$('#$idInput').datetimepicker($JSOptions);");

        return $this->textInput($options);
    }

    public function maskedInput($options = [])
    {
        $this->initWKIcon($options);
        $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield pmd-textfield-floating-label' : 'form-group pmd-textfield pmd-textfield-floating-label';

        if (isset($options['wkkeep'])) {
            $options['options']['wkkeep'] = true;
            unset($options['wkkeep']);
        }

        $options['options']['class'] = $options['options']['class'] ?: 'form-control';

        return $this->widget(MaskedInput::className(), $options);
    }

    public function snilsInput($options = [])
    {
        $options['mask'] = '999-999-999-99';

        return $this->maskedInput($options);
    }

    protected function initWKIcon(array &$options)
    {
        if (isset($options['wkicon'])) {
            $this->template = preg_replace('/(.*)\{input\}(.*)/', '$1<div class="input-group"><div style="min-width: 50px;" class="input-group-addon"><i class="fa fa-2x fa-' . $options['wkicon'] . ' pmd-sm"></i></div>{input}</div>$2', $this->template);
            $this->labelOptions['class'] .= ' pmd-input-group-label';
            unset($options['wkicon']);
        }
    }

    protected function convertDateTimeValue($model, $attribute)
    {
        $value = preg_replace('/(\d{4})-(\d{2})-(\d{2})(\s(\d{2}):(\d{2}):(\d{2}))?/', '$3.$2.$1 $5:$6:$7', $model->$attribute);

        if ($value) {
            $model->$attribute = $value;
        }
    }
}