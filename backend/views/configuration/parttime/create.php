<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\ParttimeForm */
/* @var $searchModelBuild domain\models\base\search\BuildSearch */
/* @var $dataProviderBuild yii\data\ActiveDataProvider */

$this->title = Yii::t('common/employee', 'Create Parttime');
?>
<div class="parttime-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="parttime-form">

        <?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>
        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'Parttime'),
            'content' => $this->render('_form', ['modelForm' => $modelForm, 'form' => $form]),
        ]) ?>
        <?= $form->field($modelForm, 'assignBuilds', ['enableClientValidation' => false])->hiddenInput()->label(false) ?>
        <?php ActiveForm::end(); ?>

        <?= GridView::widget([
            'id' => 'ParttimeBuildCreateGrid',
            'dataProvider' => $dataProviderBuild,
            'filterModel' => $searchModelBuild,
            'columns' => [
                'build_name',
            ],
            'crudSettings' => [
                'create' => [
                    'urlGrid' => 'configuration/spravochniki/build/index',
                    'inputName' => 'ParttimeForm[assignBuilds]',
                ],
                'delete' => [
                    'inputName' => 'ParttimeForm[assignBuilds]',
                ],
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_HOME),
                'title' => Yii::t('common/employee', 'Builds'),
            ],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Create'), ['class' => 'btn btn-success', 'form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
