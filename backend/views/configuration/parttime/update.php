<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\ParttimeForm */
/* @var $searchModelParttimeBuild domain\models\base\search\ParttimeBuildSearch */
/* @var $dataProviderParttimeBuild yii\data\ActiveDataProvider */

$this->title = \domain\models\base\Parttime::findOne(Yii::$app->request->get('id'))->dolzh->dolzh_name;
?>
<div class="parttime-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="parttime-form">
        <?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>
        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'Parttime'),
            'content' => $this->render('_form', ['modelForm' => $modelForm, 'form' => $form]),
        ]) ?>
        <?php ActiveForm::end(); ?>

        <?= GridView::widget([
            'id' => 'ParttimeBuildGrid',
            'dataProvider' => $dataProviderParttimeBuild,
            'filterModel' => $searchModelParttimeBuild,
            'columns' => [
                'build.build_name',
                [
                    'attribute' => 'parttime_build_deactive',
                    'format' => 'datetime',
                ],
            ],
            'crudSettings' => [
                'create' => ['configuration/parttime-build/create', 'employee' => Yii::$app->request->get('id')],
                'update' => 'configuration/parttime-build/update',
                'delete' => 'configuration/parttime-build/delete',
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_LIST_ALT),
                'title' => Yii::t('domain\parttime_build', 'Builds'),
            ],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Update'), ['class' => 'btn btn-primary', 'form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
