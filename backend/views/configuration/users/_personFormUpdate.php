<?php

use common\widgets\ActiveForm\ActiveForm;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelUserFormUpdate domain\forms\base\UserFormUpdate */
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
            <?= $userForm->field($modelUserFormUpdate, 'person_email')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_AT]) ?>
        </div>
    </div>
</div>