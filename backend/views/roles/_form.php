<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\GridView\services\GWCreateCrudConfig;
use common\widgets\GridView\services\GWDeleteCrudConfig;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\models\base\AuthItem */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(['id' => 'myform1']); ?>

    <?= $form->field($modelForm, 'description')->textInput(['wkkeep' => true]) ?>

    <?= $form->field($modelForm, 'assignRoles', ['enableClientValidation' => false])/*->hiddenInput()*/->label(false) ?>

    <?php ActiveForm::end(); ?>

    <?=
    GridView::widget([
        'id' => $modelForm->formName() . 'Grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'description'
        ],
        'crudSettings' => [
            'create' => GWCreateCrudConfig::set()
                ->urlGrid(['roles/index-for-roles'])
                ->inputName('RoleForm[assignRoles]'),
            'delete' => GWDeleteCrudConfig::set()
                ->inputName('RoleForm[assignRoles]'),
        ],
        'panelHeading' => [
            'icon' => FA::icon(FA::_LIST_ALT),
            'title' => Yii::t('common/roles', 'Roles'),
        ],
    ]);
    ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('common/authitem', 'Create'), ['class' => 'btn btn-success', 'form' => 'myform1']) ?>
    </div>
</div>