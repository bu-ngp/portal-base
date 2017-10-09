<?php

use common\widgets\ActiveForm\ActiveForm;
use domain\models\base\Dolzh;
use common\widgets\Select2\Select2;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeForm */
?>

<?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>

<?php
$map = \yii\helpers\ArrayHelper::map(Dolzh::find()->all(), 'dolzh_id', 'dolzh_name');

$map2 = [];
foreach ($map as $key => $value) {
    $map2[\wartron\yii2uuid\helpers\Uuid::uuid2str($key)] = $value;
}
?>

<?= $form->field($modelForm, 'dolzh_id')->select2([
    'data' => $map2,
    'options' => ['placeholder' => 'Select a state ...'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]); ?>

<?= $form->field($modelForm, 'podraz_id')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_USER_SECRET]) ?>

<?= $form->field($modelForm, 'build_id')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_USER_SECRET]) ?>

<?= $form->field($modelForm, 'employee_begin')->datetime(['wkkeep' => true, 'wkicon' => FA::_CALENDAR]) ?>

<?php ActiveForm::end(); ?>
