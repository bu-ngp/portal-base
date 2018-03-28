<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.06.2017
 * Time: 18:25
 */

namespace common\widgets\ActiveForm;


use common\widgets\PropellerAssets\DateTimePickerAsset;
use common\widgets\PropellerAssets\PropellerAsset;
use common\widgets\PropellerAssets\RadioAsset;
use common\widgets\Select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\validators\DateValidator;
use yii\validators\StringValidator;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;

/**
 * Класс полей `ActiveForm` на базе класса [\yii\bootstrap\ActiveField](https://www.yiiframework.com/doc-2.0/yii-bootstrap-activefield.html)
 */
class ActiveField extends \yii\bootstrap\ActiveField
{
    /** @var string Шаблон поля CheckBox */
    public $checkboxTemplate = "<div class=\"checkbox pmd-default-theme\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";
    /** @var string Шаблон поля RadioList */
    public $inlineRadioListTemplate = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";

    /**
     * Метод добавления элемента CheckBox.
     * Выполнен на базе [\yii\bootstrap\ActiveField::checkbox()](https://www.yiiframework.com/doc-2.0/yii-bootstrap-activefield.html#checkbox()-detail)
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkkeep`     | bool         | Сохранять состояние элемента при обновлении страницы
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'post_active')->checkbox([
     *              'wkkeep' => true,
     *          ]) ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $options
     * @param bool $enclosedByLabel
     * @return \yii\bootstrap\ActiveField
     */
    public function checkbox($options = [], $enclosedByLabel = true)
    {
        $model = $this->model;
        $attribute = $this->attribute;

        $options = array_replace([
            'label' => "<span class=\"control-label\">{$model->getAttributeLabel($attribute)}</span>",
            'labelOptions' => ['class' => 'pmd-checkbox'],
        ], $options);

        PropellerAsset::setWidget('checkbox');

        return parent::checkbox($options, $enclosedByLabel);
    }

    /**
     * Метод добавления элемента ToggleSwitch.
     * Выполнен на базе [\yii\bootstrap\ActiveField::checkbox()](https://www.yiiframework.com/doc-2.0/yii-bootstrap-activefield.html#checkbox()-detail)
     *
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkkeep`     | bool         | Сохранять состояние элемента при обновлении страницы
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'post_active')->toggleSwitch([
     *              'wkkeep' => true,
     *          ]) ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $options
     * @param bool $enclosedByLabel
     * @return \yii\bootstrap\ActiveField
     */
    public function toggleSwitch($options = [], $enclosedByLabel = true)
    {
        $model = $this->model;
        $attribute = $this->attribute;
        $this->checkboxTemplate = "<div style=\"margin-top: 30px;\" class=\"pmd-switch\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";

        $options = array_replace([
            'label' => "<span style=\"float: left; margin-right: 10px;\" class=\"pmd-switch-label\"></span><span style=\"float: right;\" class=\"control-label\">{$model->getAttributeLabel($attribute)}</span>",
        ], $options);

        PropellerAsset::setWidget('toggleswitch');

        return parent::checkbox($options, $enclosedByLabel);
    }

    /**
     * Метод добавления элемента TextInput.
     * Выполнен на базе [\yii\widgets\ActiveField::textInput()](https://www.yiiframework.com/doc/api/2.0/yii-widgets-activefield#textInput()-detail)
     *
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkkeep`     | bool         | Сохранять состояние элемента при обновлении страницы
     * `wkicon`     | string       | Класс иконки `FontAwesome`, которая отобразится слева от элемента
     * `noFloat`    | bool         | Отключить плавающую подпись элемента TextInput, по умолчанию `false`
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'post_name')->textInput([
     *              'wkkeep' => true,
     *              'wkicon' => FA::_SQUARE,
     *              'noFloat' => true,
     *          ]) ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $options
     * @return \yii\bootstrap\ActiveField
     */
    public function textInput($options = [])
    {
        $this->initWKIcon($options);
        $noFloat = ArrayHelper::getValue($options, 'noFloat', false);
        $floatLabel = $noFloat ? '' : ' pmd-textfield-floating-label';
        $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield' . $floatLabel : "form-group pmd-textfield$floatLabel";

        PropellerAsset::setWidget('input');

        return parent::textInput($options);
    }

    /**
     * Метод добавления элемента PasswordInput.
     * Выполнен на базе [\yii\widgets\ActiveField::passwordInput()](https://www.yiiframework.com/doc/api/2.0/yii-widgets-activefield#passwordInput()-detail)
     *
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkicon`     | string       | Класс иконки `FontAwesome`, которая отобразится слева от элемента
     * `noFloat`    | bool         | Отключить плавающую подпись элемента TextInput, по умолчанию `false`
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'user_password')->passwordInput([
     *              'wkicon' => FA::_LOCK,
     *              'noFloat' => true,
     *          ]) ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $options
     * @return \yii\bootstrap\ActiveField
     */
    public function passwordInput($options = [])
    {
        $this->initWKIcon($options);
        $noFloat = ArrayHelper::getValue($options, 'noFloat', false);
        $floatLabel = $noFloat ? '' : ' pmd-textfield-floating-label';
        $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield' . $floatLabel : "form-group pmd-textfield$floatLabel";

        PropellerAsset::setWidget('input');

        return parent::passwordInput($options);
    }

    /**
     * Метод добавления элемента radioList.
     * Выполнен на базе [\yii\bootstrap\ActiveField::radioList()](https://www.yiiframework.com/doc-2.0/yii-bootstrap-activefield.html#radioList()-detail)
     *
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkkeep`     | bool         | Сохранять состояние элемента при обновлении страницы
     * `titles`     | array        | Список наименований для ключей значений radioList (например, если значения иконки)
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'user_sex')->radioList([
     *               1 => '<i class="fa fa-2x fa-male"></i>',
     *               2 => '<i class="fa fa-2x fa-female"></i>',
     *          ], [
     *              'wkkeep' => true,
     *              'titles' => [
     *                  1 => 'Мужской',
     *                  2 => 'Женский',
     *              ],
     *          ]) ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $items
     * @param array $options
     * @return \yii\bootstrap\ActiveField
     */
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

        PropellerAsset::setWidget('radiolist');

        parent::radioList($items, $options);

        return $this;
    }

    /**
     * Метод добавления элемента Datetime, для выбора значения даты.
     * Выполнен на базе [\yii\widgets\ActiveField::textInput()](https://www.yiiframework.com/doc/api/2.0/yii-widgets-activefield#textInput()-detail) с использованием jquery плагина `bootstrap-datetimepicker.js`
     * Метод проверяет атрибут на существование валидатора [\yii\validators\DateValidator](https://www.yiiframework.com/doc/api/2.0/yii-validators-datevalidator). Если имеется, то дата преобразуется в русский формат.
     *
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkkeep`     | bool         | Сохранять состояние элемента при обновлении страницы
     * `wkicon`     | string       | Класс иконки `FontAwesome`, которая отобразится слева от элемента
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'post_date')->datetime([
     *              'wkkeep' => true,
     *              'wkicon' => FA::_CALENDAR_TIMES_O,
     *          ]) ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $options
     * @return \yii\bootstrap\ActiveField
     */
    public function datetime($options = [])
    {
        $idInput = strtolower($this->model->formName()) . '-' . $this->attribute;

        $view = $this->form->getView();

        $format = 'DD.MM.YYYY';
        foreach ($this->model->getActiveValidators($this->attribute) as $validator) {
            if ($validator instanceof DateValidator) {
                if ($validator->format === 'yyyy-MM-dd') {
                    $format = 'DD.MM.YYYY';
                    // $format = 'YYYY-MM-DD';
                }

                if ($validator->format === 'yyyy-MM-dd HH:mm:ss') {
                    $format = 'DD.MM.YYYY HH:mm:ss';
                    // $format = 'YYYY-MM-DD HH:mm:ss';
                }
            }
        }

        $this->convertDateTimeValue($this->model, $this->attribute);

        $JSOptions = Json::encode([
            'locale' => 'ru',
            'format' => $format,
            'useCurrent' => false,
            'widgetPositioning' => [ // Календарь заходит под грид на табе
                'vertical' => 'top',
            ],
            // 'extraFormats' => ['DD.MM.YYYY'],
        ]);

        PropellerAsset::setWidget('datetimepicker');

        $view->registerJs("$('#$idInput').datetimepicker($JSOptions);");

        return $this->textInput($options);
    }

    /**
     * Метод добавления элемента maskedInput, для ввода значения в текстовое поле по маске.
     * Выполнен на базе [\yii\widgets\MaskedInput](https://www.yiiframework.com/doc/api/2.0/yii-widgets-maskedinput)
     *
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkkeep`     | bool         | Сохранять состояние элемента при обновлении страницы
     * `wkicon`     | string       | Класс иконки `FontAwesome`, которая отобразится слева от элемента
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'user_phone')->maskedInput([
     *              'mask' => '8-(9999)-99-99-99',
     *              'wkkeep' => true,
     *              'wkicon' => FA::_PHONE,
     *          ]) ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $options
     * @return \yii\bootstrap\ActiveField
     */
    public function maskedInput($options = [])
    {
        $this->initWKIcon($options);
        $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield pmd-textfield-floating-label' : 'form-group pmd-textfield pmd-textfield-floating-label';

        if (isset($options['wkkeep'])) {
            $options['options']['wkkeep'] = true;
            unset($options['wkkeep']);
        }

        $options['options']['class'] = $options['options']['class'] ?: 'form-control';

        $options['clientOptions'] = [
            'showMaskOnHover' => false,
            //  'autoUnmask' => true,
        ];

        return $this->widget(MaskedInput::className(), $options);
    }

    /**
     * Метод добавления элемента snilsInput, для ввода значения СНИЛСа в текстовое поле.
     * Выполнен на базе [\yii\widgets\MaskedInput](https://www.yiiframework.com/doc/api/2.0/yii-widgets-maskedinput)
     *
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkkeep`     | bool         | Сохранять состояние элемента при обновлении страницы
     * `wkicon`     | string       | Класс иконки `FontAwesome`, которая отобразится слева от элемента
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'user_snils')->snilsInput([
     *              'wkkeep' => true,
     *              'wkicon' => FA::_COPYRIGHT,
     *          ]) ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $options
     * @return \yii\bootstrap\ActiveField
     */
    public function snilsInput($options = [])
    {
        $options['mask'] = '999-999-999 99';

        return $this->maskedInput($options);
    }


    /**
     * Метод добавления элемента select2, для выбора из списка, с помощью jquery плагина `Select2`.
     * Выполнен на базе [[\common\widgets\Select2\Select2]]
     *
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkkeep`     | bool         | Сохранять состояние элемента при обновлении страницы
     * `wkicon`     | string       | Класс иконки `FontAwesome`, которая отобразится слева от элемента
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'user_id')->select2([
     *              'activeRecordClass' => User::className(),
     *              'queryCallback' => UserQuery::select(),
     *              'wkkeep' => true,
     *              'wkicon' => FA::_USER,
     *         ]); ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $options
     * @return \yii\bootstrap\ActiveField
     */
    public function select2($options = [])
    {
        $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield-select2 pmd-textfield-select2-floating-label' : 'form-group pmd-textfield-select2 pmd-textfield-select2-floating-label';
        // $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield' : 'form-group pmd-textfield';

        if ($options['wkicon']) {
            $this->labelOptions['class'] .= ' wk-label-with-icon';
        }

        return $this->widget(Select2::className(), $options);
    }


    /**
     * Метод добавления элемента Textarea, для ввода текста.
     * Выполнен на базе [\yii\widgets\ActiveField](https://www.yiiframework.com/doc/api/2.0/yii-widgets-activefield#textarea()-detail).
     *
     * Проверяет на наличие валидотора [\yii\validators\StringValidator](https://www.yiiframework.com/doc/api/2.0/yii-validators-stringvalidator) атрибута модели, и отображает снизу количество оставшихся символов, в случае заданного свойства `max` у валидатора.
     *
     * Дополнительные опции:
     *
     * Ключ массива | Тип значения | Описание
     * ------------ | ------------ | ------------
     * `wkkeep`     | bool         | Сохранять состояние элемента при обновлении страницы
     * `wkicon`     | string       | Класс иконки `FontAwesome`, которая отобразится слева от элемента
     *
     * ```php
     *     <?php $form = ActiveForm::begin(); ?>
     *
     *     <?= $form->field($modelForm, 'user_description')->textarea([
     *              'wkkeep' => true,
     *              'wkicon' => FA::_USER,
     *          ]) ?>
     *
     *     <?php ActiveForm::end(); ?>
     * ```
     *
     * @param array $options
     * @return \yii\bootstrap\ActiveField
     */
    public function textarea($options = [])
    {
        $this->initWKIcon($options);
        $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' pmd-textfield pmd-textfield-floating-label' : 'form-group pmd-textfield pmd-textfield-floating-label';

        PropellerAsset::setWidget('input');

        $stringValidator = array_values(array_filter($this->model->getActiveValidators($this->attribute), function ($validator) {
            if ($validator instanceof StringValidator) {
                return isset($validator->max);
            }
            return false;
        }));

        if ($stringValidator) {
            $this->template = "{label}\n<div class=\"wk-textarea-container\">{input}<div wk-chars-max=\"{$stringValidator[0]->max}\" class=\"wk-chars-counter\">50/100</div></div>\n{hint}\n{error}";
        }

        return parent::textarea($options);
    }

    protected function initWKIcon(array &$options)
    {
        if (isset($options['wkicon'])) {
            $this->template = preg_replace('/(.*)\{input\}(.*)/', '$1<div class="input-group"><div style="min-width: 50px;" class="input-group-addon"><i class="fa fa-2x fa-' . $options['wkicon'] . ' pmd-sm"></i></div>{input}</div>$2', $this->template);
            $this->labelOptions['class'] .= ' pmd-input-group-label wk-input-with-icon';
            $this->options['class'] = isset($this->options['class']) ? $this->options['class'] . ' wk-form-group-with-icon' : 'wk-form-group-with-icon';
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