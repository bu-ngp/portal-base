<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelForm \yii\base\Model */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<?php
$isNew = $modelForm instanceof \domain\forms\base\RoleForm;
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(['id' => 'myform1']); ?>

    <?= $form->field($modelForm, 'description')->textInput(['wkkeep' => true]) ?>

    <?= $isNew ? $form->field($modelForm, 'assignRoles', ['enableClientValidation' => false])->hiddenInput()->label(false) : '' ?>

    <?php ActiveForm::end(); ?>

    <?=
    GridView::widget(array_merge([
        'id' => $modelForm->formName() . 'Grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'description'
        ],
        'crudSettings' => $crudSettings,
        'panelHeading' => [
            'icon' => FA::icon(FA::_LIST_ALT),
            'title' => Yii::t('common/roles', 'Roles'),
        ],
    ],
        isset($gridInject) ? [
            'gridInject' => $gridInject,
        ] : []
    ));
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common/authitem', $isNew ? 'Create' : 'Update'), ['class' => 'btn ' . ($isNew ? 'btn-success' : 'btn-primary'), 'form' => 'myform1']) ?>
    </div>
</div>