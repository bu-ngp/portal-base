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
            'label' => Yii::t('common/person', 'Main'),
            'content' => Panel::widget([
                    'label' => 'Пользователь',
                    'content' => $form->field($filterModel, 'person_active_mark')->checkbox()
                        . $form->field($filterModel, 'person_parttime_exist_mark')->checkbox()
                        . $form->field($filterModel, 'person_parttime_not_exist_mark')->checkbox()
                        . $form->field($filterModel, 'person_roles_exist_mark')->checkbox()
                        . $form->field($filterModel, 'person_roles_not_exist_mark')->checkbox()
                    ,
                ]) . Panel::widget([
                    'label' => 'Профиль',
                    'content' => $form->field($filterModel, 'profile_inn_not_exist_mark')->checkbox()
                        . $form->field($filterModel, 'profile_snils_not_exist_mark')->checkbox()
                        . $form->field($filterModel, 'profile_dr_not_exist_mark')->checkbox(),
                ]),
        ],
    ],
]) ?>

<?php ActiveFilterForm::end(); ?>