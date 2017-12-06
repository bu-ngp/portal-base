<?php

use ngp\services\forms\ConfigOfomsUpdateForm;
use rmrevin\yii\fontawesome\FA;
use common\widgets\ActiveForm\ActiveForm;
use common\widgets\Html\Html;

/* @var $this yii\web\View */
/* @var $modelForm ConfigOfomsUpdateForm */

$this->title = Yii::t('ngp/config-ofoms', 'Update Ofoms Settings');
?>
<div class="config-ofoms-update content-container">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="config-ofoms-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'config_ofoms_url')->textInput(['wkkeep' => true, 'wkicon' => FA::_SERVER]) ?>

        <?= $form->field($modelForm, 'config_ofoms_url_prik')->textInput(['wkkeep' => true, 'wkicon' => FA::_SERVER]) ?>

        <?= $form->field($modelForm, 'config_ofoms_login')->textInput(['wkkeep' => true, 'wkicon' => FA::_USER_SECRET]) ?>

        <?= $form->field($modelForm, 'config_ofoms_password')->passwordInput(['wkicon' => FA::_LOCK]) ?>

        <?= $form->field($modelForm, 'config_ofoms_remote_host_name')->textInput(['wkkeep' => true, 'wkicon' => FA::_SERVER]) ?>

        <?= $form->field($modelForm, 'config_ofoms_active')->toggleSwitch(['wkkeep' => true]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::updateButton() ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>