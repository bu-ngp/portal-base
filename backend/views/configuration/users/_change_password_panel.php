<?php

use common\widgets\ActiveForm\ActiveForm;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelChangeUserPasswordForm domain\forms\base\ChangeUserPasswordForm */
/* @var $changeUserPasswordForm ActiveForm */
?>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $changeUserPasswordForm->field($modelChangeUserPasswordForm, 'person_fullname')->textInput(['disabled' => true, 'wkicon' => FA::_ID_CARD]) ?>
        </div>
        <div class="col-xs-6">
            <?= $changeUserPasswordForm->field($modelChangeUserPasswordForm, 'person_username')->textInput(['disabled' => true, 'wkicon' => FA::_USER_SECRET]) ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $changeUserPasswordForm->field($modelChangeUserPasswordForm, 'person_password')->passwordInput(['wkicon' => FA::_LOCK]) ?>
        </div>
        <div class="col-xs-6">
            <?= $changeUserPasswordForm->field($modelChangeUserPasswordForm, 'person_password_repeat')->passwordInput(['wkicon' => FA::_UNLOCK_ALT]) ?>
        </div>
    </div>
</div>