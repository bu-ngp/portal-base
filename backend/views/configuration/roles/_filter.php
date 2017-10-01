<?php
use common\widgets\ActiveForm\ActiveFilterForm;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;

/**
 * @var \yii\base\Model $filterModel
 */
?>

<?php $form = ActiveFilterForm::begin(); ?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => Yii::t('domain/authitem', 'Main'),
            'content' => Panel::widget([
                    'label' => 'Системные',
                    'content' => $form->field($filterModel, 'authitem_system_roles_mark')->checkbox(),
                ]) . Panel::widget([
                    'label' => 'Пользовательские',
                    'content' => $form->field($filterModel, 'authitem_users_roles_mark')->checkbox(),
                ]),
        ],
        [
            'label' => Yii::t('domain/authitem', 'Additional'),
            'content' => $form->field($filterModel, 'authitem_name')->textInput(),
        ],
    ],
]) ?>

<?php ActiveFilterForm::end(); ?>