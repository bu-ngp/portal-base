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

        <?php $form = ActiveForm::begin(['id' => 'myform1']); ?>

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
//                'create' => GWAddCrudConfigForUpdate::set()
//                    ->urlGrid(['roles/index-for-roles'/*, 'excludeId' => $modelForm->getPrimaryKey()*/])
//                    ->urlAction(['roles/update-remove-roles', 'id' => $modelForm->getPrimaryKey()])
//                    ->excludeFromId($modelForm->getPrimaryKey()),
//                'delete' => GWDeleteCrudConfig::set()
//                    ->inputName('RoleForm[assignRoles]'),
                'delete' => 'roles/update-remove-roles',
            ],
            'gridInject' => [
                'mainField' => 'parent',
                'mainIdParameterName' => 'id',
                'foreignField' => 'child',
                'saveFunc' => function (\yii\db\ActiveRecord $model, $mainId, $mainField, $foreignField, $foreignId) {
                    $model->$mainField = $mainId;
                    $model->$foreignField = $foreignId;
                    $model->save();

                    return $model->getErrors();
                },
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
</div>
