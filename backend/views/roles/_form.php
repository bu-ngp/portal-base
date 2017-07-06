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

    <?= $form->field($modelForm, 'description')->textInput(['wkkeep' => true]) ?>

    <?= $form->field($modelForm, 'assignRoles', ['enableClientValidation' => false])/*->hiddenInput()->label(false)*/ ?>

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
            'create' => '#',
            'delete' => ['roles/delete'],
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

<div tabindex="-1" class="modal fade" id="large-dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h3 class="pmd-card-title-text">Two-line item</h3>
            </div>
            <div class="modal-body grid-content"></div>
            <div class="pmd-modal-action">
                <button data-dismiss="modal" type="button"
                        class="btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-primary"><i
                        class="material-icons pmd-sm">share</i></button>
                <button data-dismiss="modal" type="button"
                        class="btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-primary"><i
                        class="material-icons pmd-sm">thumb_up</i></button>
            </div>
        </div>
    </div>
</div>