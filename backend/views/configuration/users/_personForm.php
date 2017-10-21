<?php

use common\widgets\ActiveForm\ActiveForm;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelUserForm domain\forms\base\UserForm */
/* @var $userForm ActiveForm */
?>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $userForm->field($modelUserForm, 'person_fullname')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_ID_CARD]) ?>
        </div>
        <div class="col-xs-6">
            <?= $userForm->field($modelUserForm, 'person_username')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_USER_SECRET]) ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $userForm->field($modelUserForm, 'person_password')->passwordInput(['wkicon' => FA::_LOCK]) ?>
        </div>
        <div class="col-xs-6">
            <?= $userForm->field($modelUserForm, 'person_password_repeat')->passwordInput(['wkicon' => FA::_UNLOCK_ALT]) ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $userForm->field($modelUserForm, 'person_email')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_AT]) ?>
        </div>
    </div>
</div>

<?= $userForm->field($modelUserForm, 'assignRoles', ['enableClientValidation' => false])->hiddenInput()->label(false) ?>
