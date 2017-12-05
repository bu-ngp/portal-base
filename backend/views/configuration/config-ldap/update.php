<?php

use domain\forms\base\RoleUpdateForm;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm RoleUpdateForm */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/config-ldap', 'Update Ldap Settings');
?>
<div class="config-ldap-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="config-ldap-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'config_ldap_host')->textInput(['wkkeep' => true, 'wkicon' => FA::_SERVER]) ?>

        <?= $form->field($modelForm, 'config_ldap_port')->maskedInput(['mask' => '9{1,5}', 'wkkeep' => true, 'wkicon' => FA::_LINK]) ?>

        <?= $form->field($modelForm, 'config_ldap_admin_login')->textInput(['wkkeep' => true, 'wkicon' => FA::_USER_SECRET]) ?>

        <?= $form->field($modelForm, 'config_ldap_admin_password')->passwordInput(['wkicon' => FA::_LOCK]) ?>

        <?= $form->field($modelForm, 'config_ldap_active')->toggleSwitch(['wkkeep' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>