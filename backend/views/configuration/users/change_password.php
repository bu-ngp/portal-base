<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $modelChangeUserPasswordForm domain\forms\base\ChangeUserPasswordForm */

$this->title = Yii::t('common/person', $modelChangeUserPasswordForm->person_fullname);
?>
<div class="user-change-password content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

    <div class="change-password-form">
        <?php $changeUserPasswordForm = ActiveForm::begin(['id' => $modelChangeUserPasswordForm->formName()]); ?>

        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'User'),
            'content' => $this->render('_change_password_panel', ['modelChangeUserPasswordForm' => $modelChangeUserPasswordForm, 'changeUserPasswordForm' => $changeUserPasswordForm]),
        ]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::updateButton() ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>