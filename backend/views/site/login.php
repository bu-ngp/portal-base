<?php

/* @var $this yii\web\View */

use common\widgets\ActiveForm\ActiveForm;
use yii\bootstrap\Html;

/* @var $form ActiveForm */
/* @var $model \domain\forms\base\LoginForm */


$this->title = Yii::t('common/login', 'Login Page');
?>
<div class="site-login">
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6 wk-login-container-logo">
            <i class="fa fa-lock"></i>
            <p><?= Yii::t('common/login', 'Enter to close part of Portal') ?></p>
        </div>
        <div class="col-lg-3"></div>
    </div>
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6 wk-login-container">

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username', ['options' => ['class' => 'form-group pmd-textfield form-group-lg']])->textInput(['autofocus' => true, 'class' => 'form-control input-group-lg']) ?>

            <?= $form->field($model, 'password', ['options' => ['class' => 'form-group pmd-textfield form-group-lg']])->passwordInput(['class' => 'form-control input-group-lg']) ?>

            <?= $form->field($model, 'rememberMe')->toggleSwitch() ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('common/login', 'Login'), ['class' => 'btn btn-block btn-lg', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-3"></div>
    </div>
</div>
