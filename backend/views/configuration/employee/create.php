<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\Html\Html;
use common\widgets\Panel\Panel;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeHistoryForm */
/* @var $searchModelBuild domain\models\base\search\BuildSearch */
/* @var $dataProviderBuild yii\data\ActiveDataProvider */

$this->title = Yii::t('common/employee', 'Create Employee');
?>
<div class="user-create content-container">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">
        <?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>
        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'Employee'),
            'content' => $this->render('_form', ['modelForm' => $modelForm, 'form' => $form]),
        ]) ?>
        <?= $form->field($modelForm, 'assignBuilds', ['enableClientValidation' => false])->hiddenInput()->label(false) ?>
        <?php ActiveForm::end(); ?>

        <?= GridView::widget([
            'id' => 'EmployeeHistoryBuildCreateGrid',
            'dataProvider' => $dataProviderBuild,
            'filterModel' => $searchModelBuild,
            'columns' => [
                'build_name',
            ],
            'crudSettings' => [
                'create' => [
                    'urlGrid' => 'configuration/spravochniki/build/index',
                    'inputName' => 'EmployeeHistoryForm[assignBuilds]',
                ],
                'delete' => [
                    'inputName' => 'EmployeeHistoryForm[assignBuilds]',
                ],
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_HOME),
                'title' => Yii::t('common/employee', 'Builds'),
            ],
        ]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::createButton(['form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
