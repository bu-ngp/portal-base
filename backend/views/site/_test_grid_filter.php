<?php

use common\widgets\ActiveForm\ActiveFilterForm;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use rmrevin\yii\fontawesome\FA;

/**
 * @var \yii\base\Model $filterModel
 */
?>

<?php $form = ActiveFilterForm::begin(); ?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Select2',
            'content' => Panel::widget([
                'label' => 'Select2',
                'content' => $form->field($filterModel, 'dolzh_select2')->select2([
                    'activeRecordClass' => \domain\models\base\Dolzh::className(),
                    'activeRecordAttribute' => 'dolzh_id',
                    'queryCallback' => \domain\queries\DolzhQuery::select(),
                    'ajaxConfig' => [
                        'searchAjaxCallback' => \domain\queries\DolzhQuery::search(),
                    ],
                    'multiple' => true,
                    'wkicon' => FA::_WINDOW_RESTORE,
                ]),
            ]),
        ],
        [
            'label' => 'Other',
            'content' => /*Panel::widget([
                    'label' => 'Other',
                    'content' => $form->field($filterModel, 'authitem_system_roles_mark')->checkbox(),
                ])*/
                '',
        ],
    ],
]) ?>

<?php ActiveFilterForm::end(); ?>