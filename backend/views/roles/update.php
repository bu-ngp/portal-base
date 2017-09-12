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

        <?= $this->render('_grid', [
            'modelForm' => $modelForm,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'gridConfig' => [
                'crudSettings' => [
                    'create' => [
                        'urlGrid' => 'roles/index-for-roles',
                    ],
                    'delete' => [
                        'url' => 'roles/delete-role',
                        'beforeRender' => function ($model) {
                            /** @var AuthItem $model */

                            return $_GET['id'] && AuthItemChild::find()
                                ->joinWith(['parent0'])
                                ->andWhere([
                                    'child' => $model->name,
                                    'Parent0.name' => $_GET['id'],
                                    'Parent0.view' => 0,
                                ])
                                ->andWhere(['not', ['parent' => 'Administrator']])
                                ->one();
                        },
                    ],
                ],
                'gridInject' => [
                    'mainField' => 'parent',
                    'mainIdParameterName' => 'id',
                    'foreignField' => 'child',
                    'modelClassName' => 'domain\models\base\AuthItemChild',
                ],
            ],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common/authitem', 'Update'), ['class' => 'btn btn-primary', 'form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
