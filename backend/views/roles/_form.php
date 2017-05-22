<?php

use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm domain\models\base\AuthItem */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(['id' => 'myform1']); ?>

    <?= $form->field($modelForm, 'description')->textInput() ?>

    <?= $form->field($modelForm, 'assignRoles')/*->hiddenInput()->label(false) */?>

    <?php ActiveForm::end(); ?>

    <?=
    GridView::widget([
        'id' => $modelForm->formName() . 'Grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'description'
        ],
        'customizeSettings' => [
            'filterShow' => true,
            'exportShow' => true,
            'customizeShow' => true,
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
