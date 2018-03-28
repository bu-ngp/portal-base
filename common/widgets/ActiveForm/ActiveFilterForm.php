<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.06.2017
 * Time: 18:22
 */

namespace common\widgets\ActiveForm;


/**
 * Класс формы для использования в модальных окнах дополнительных фильтров.
 *
 * ```php
 * $form = ActiveFilterForm::begin();
 * echo Tabs::widget([
 *         'items' => [
 *             [
 *                 'label' => 'Основные',
 *                 'content' => Panel::widget([
 *                     'label' => 'Системные',
 *                     'content' => $form->field($filterModel, 'authitem_system_roles_mark')->checkbox(),
 *                 ]) . Panel::widget([
 *                     'label' => 'Пользовательские',
 *                     'content' => $form->field($filterModel, 'authitem_users_roles_mark')->checkbox(),
 *                 ]),
 *             ],
 *             [
 *                 'label' => 'Дополнительные',
 *                 'content' => $form->field($filterModel, 'authitem_name')->textInput(),
 *             ],
 *         ],
 *     ])
 * ActiveFilterForm::end();
 * ```
 */
class ActiveFilterForm extends ActiveForm
{
    /**
     * @var string Переопределен класс ActiveField на [[ActiveField]]
     */
    public $fieldClass = 'common\widgets\ActiveForm\ActiveField';

    /**
     * Метод `\yii\widgets\ActiveForm` переопределен для использования в модальных окнах дополнительных фильтров.
     * Добавлен HTML класс `filter-marked`.
     *
     * @param \yii\base\Model $model
     * @param string $attribute
     * @param array $options
     * @return ActiveField
     */
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