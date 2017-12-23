<?php

/* @var $this yii\web\View */

use common\widgets\ActiveForm\ActiveForm;
use yii\bootstrap\Html;

/* @var $form ActiveForm */
/* @var $model \domain\forms\base\LoginForm */


$this->title = Yii::t('common/login', 'Login Page');
?>
<div class="site-login">
    <div class="wk-login-container content-container">
        <div>
            <div class="wk-login-container-logo">
                <i class="fa fa-lock"></i>
                <p><?= Yii::t('common/login', 'Enter to close part of Portal') ?></p>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">

                    </h3>
                </div>
                <div class="panel-body">

                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                    <?= $form->field($model, 'username', ['options' => ['class' => 'form-group pmd-textfield form-group-lg']])->textInput(['noFloat' => true, 'autofocus' => true, 'class' => 'form-control input-group-lg']) ?>

                    <?= $form->field($model, 'password', ['options' => ['class' => 'form-group pmd-textfield form-group-lg']])->passwordInput(['noFloat' => true, 'class' => 'form-control input-group-lg']) ?>

                    <?= $form->field($model, 'rememberMe')->toggleSwitch() ?>

                    <div class="form-group wk-login-button">
                        <?= Html::submitButton(Yii::t('common/login', 'Login'), ['class' => 'btn btn-block btn-lg', 'name' => 'login-button']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>
