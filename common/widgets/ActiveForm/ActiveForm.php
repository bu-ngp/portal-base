<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.06.2017
 * Time: 18:22
 */

namespace common\widgets\ActiveForm;

/**
 * Класс формы ActiveForm
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
 */
class ActiveForm extends \yii\bootstrap\ActiveForm
{
    /**
     * @var string Переопределен класс ActiveField на [[ActiveField]]
     */
    public $fieldClass = 'common\widgets\ActiveForm\ActiveField';

    /**
     * @return \yii\bootstrap\ActiveField
     */
    public function field($model, $attribute, $options = [])
    {
        //Для PHPStorm подсветки
        return parent::field($model, $attribute, $options);
    }
}