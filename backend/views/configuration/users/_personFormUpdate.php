<?php

use common\assets\UserAsset;
use common\widgets\ActiveForm\ActiveForm;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelUserFormUpdate domain\forms\base\UserFormUpdate */
/* @var $userForm ActiveForm */

if (!$modelUserFormUpdate->isHaveActiveEmployee()) {
    $hint = 'Доступно в случае имеющихся активных специальностей';
} elseif (!$modelUserFormUpdate->person_fired) {
    $hint = 'В случае заполнения даты увольнения, все специальности и совмещения закроются датой увольнения';
} else {
    $hint = '';
}

UserAsset::register($this);
?>
<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $userForm->field($modelUserFormUpdate, 'person_fullname')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_ID_CARD]) ?>
        </div>
        <div class="col-xs-6">
            <?= $userForm->field($modelUserFormUpdate, 'person_username')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_USER_SECRET]) ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $userForm->field($modelUserFormUpdate, 'person_code')->textInput(['disabled' => true, 'wkicon' => FA::_KEY]) ?>
        </div>
        <div class="col-xs-6">
            <?= $userForm->field($modelUserFormUpdate, 'person_email')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_AT]) ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $userForm->field($modelUserFormUpdate, 'person_hired')->datetime(['disabled' => true, 'maxlength' => true, 'wkicon' => FA::_CALENDAR_PLUS_O])->hint('Выводится автоматически при добавлении специальности') ?>
        </div>
        <div class="col-xs-6">
            <?= $userForm->field($modelUserFormUpdate, 'person_fired')->datetime(['disabled' => !$modelUserFormUpdate->isHaveActiveEmployee(), 'wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_CALENDAR_TIMES_O])->hint($hint) ?>
        </div>
    </div>
</div>