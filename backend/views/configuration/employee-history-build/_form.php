<?php

use common\widgets\ActiveForm\ActiveForm;
use domain\models\base\Build;
use domain\queries\BuildQuery;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeBuildForm */
?>

<?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>

<?= $form->field($modelForm, 'build_id')->select2([
    'activeRecordClass' => Build::className(),
    'queryCallback' => BuildQuery::select(),
    'ajaxConfig' => [
        'searchAjaxCallback' => BuildQuery::search(),
    ],
    'wkkeep' => true,
    'wkicon' => FA::_HOME,
    'selectionGridUrl' => ['configuration/spravochniki/build/index'],
]); ?>

<?= $form->field($modelForm, 'employee_history_build_deactive')->datetime(['wkkeep' => true, 'wkicon' => FA::_CALENDAR_TIMES_O]) ?>

<?php ActiveForm::end(); ?>