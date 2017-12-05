<?php


use common\widgets\ActiveForm\ActiveForm;
use common\widgets\Html\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\models\base\AuthItem */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/roles', 'Create Auth Item');
?>
<div class="auth-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="auth-item-form">

        <?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>

        <?= $form->field($modelForm, 'description')->textInput(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'ldap_group')->textInput(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'assignRoles', ['enableClientValidation' => false])->hiddenInput()->label(false) ?>

        <?php ActiveForm::end(); ?>

        <?= $this->render('_grid', [
            'modelForm' => $modelForm,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'gridConfig' => [
                'crudSettings' => [
                    'create' => [
                        'urlGrid' => 'configuration/roles/index-for-roles',
                        'inputName' => 'RoleForm[assignRoles]',
                    ],
                    'delete' => [
                        'inputName' => 'RoleForm[assignRoles]',
                    ],
                ],
            ],
        ]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::createButton(['form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>