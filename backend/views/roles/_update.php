<?php

use common\widgets\GridView\services\GWAddCrudConfigForUpdate;
use domain\forms\base\RoleUpdateForm;
use yii\helpers\Html;
use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\GridView\services\GWDeleteCrudConfig;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelForm RoleUpdateForm */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
        'modelClass' => 'Auth Item',
    ]) . $modelForm->description;
?>
<div class="auth-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="auth-item-form">

        <?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>

        <?= $form->field($modelForm, 'description')->textInput(['wkkeep' => true]) ?>

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
                'create' => [
                    'class' => 'common\widgets\GridView\services\GWAddCrudConfigForUpdate',
                    'urlGrid' => ['roles/index-for-roles'],
                ],
                'delete' => ['roles/delete-role'],
            ],
            'gridInject' => [
                'class' => 'common\widgets\GridView\services\GWSaveModelForUpdate',
                'mainField' => 'parent',
                'mainIdParameterName' => 'id',
                'foreignField' => 'child',
                'modelClassName' => 'domain\models\base\AuthItemChild',
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_LIST_ALT),
                'title' => Yii::t('common/roles', 'Roles'),
            ],
        ]);
        ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common/authitem', 'Update'), ['class' => 'btn btn-primary', 'form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
