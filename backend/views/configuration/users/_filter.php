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
            'content' => $form->field($filterModel, 'person_active_mark')->checkbox(),
        ],
    ],
]) ?>

<?php ActiveFilterForm::end(); ?>