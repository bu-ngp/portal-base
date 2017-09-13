<?php

use domain\forms\base\RoleUpdateForm;
use domain\models\base\AuthItem;
use domain\models\base\AuthItemChild;
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

        <?= $form->field($modelForm, 'config_ldap_host')->textInput(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'config_ldap_port')->textInput(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'config_ldap_active')->checkbox(['wkkeep' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common/authitem', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
</div>
