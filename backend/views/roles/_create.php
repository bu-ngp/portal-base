<?php

use yii\helpers\Html;
use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelForm domain\models\base\AuthItem */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/authitem', 'Create Auth Item');
?>
    <div class="auth-item-create">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="auth-item-form">

            <?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>

            <?= $form->field($modelForm, 'description')->textInput(['wkkeep' => true]) ?>

            <?= $form->field($modelForm, 'assignRoles', ['enableClientValidation' => false])->hiddenInput()->label(false) ?>

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
                        'urlGrid' => 'roles/index-for-roles',
                        'inputName' => 'RoleForm[assignRoles]',
                    ],
                    'delete' => [
                        'inputName' => 'RoleForm[assignRoles]',
                    ],
                ],
                'panelHeading' => [
                    'icon' => FA::icon(FA::_LIST_ALT),
                    'title' => Yii::t('common/roles', 'Roles'),
                ],
            ]);
            ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('common/authitem', 'Create'), ['class' => 'btn btn-success', 'form' => $modelForm->formName()]) ?>
            </div>

        </div>
    </div>

<?php
$this->registerJs(file_get_contents(__DIR__ . '/roleForm.js'));
?>