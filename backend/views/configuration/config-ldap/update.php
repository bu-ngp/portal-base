<?php

use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;
use domain\forms\base\RoleUpdateForm;
use rmrevin\yii\fontawesome\FA;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm RoleUpdateForm */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/config-ldap', 'Update Ldap Settings');
?>
<div class="config-ldap-update content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

    <div class="config-ldap-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'config_ldap_host')->textInput(['wkkeep' => true, 'wkicon' => FA::_SERVER]) ?>

        <?= $form->field($modelForm, 'config_ldap_port')->maskedInput(['mask' => '9{1,5}', 'wkkeep' => true, 'wkicon' => FA::_LINK]) ?>

        <?= $form->field($modelForm, 'config_ldap_admin_login')->textInput(['wkkeep' => true, 'wkicon' => FA::_USER_SECRET]) ?>

        <?= $form->field($modelForm, 'config_ldap_admin_password')->passwordInput(['wkicon' => FA::_LOCK]) ?>

        <?= $form->field($modelForm, 'config_ldap_active')->toggleSwitch(['wkkeep' => true]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::updateButton() ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>