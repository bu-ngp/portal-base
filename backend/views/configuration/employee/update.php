<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;
use common\widgets\Panel\Panel;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeHistoryForm */
/* @var $searchModelEmployeeHB domain\models\base\search\EmployeeHistoryBuildSearch */
/* @var $dataProviderEmployeeHB yii\data\ActiveDataProvider */

$this->title = \domain\models\base\EmployeeHistory::findOne(Yii::$app->request->get('id'))->dolzh->dolzh_name;
?>
<div class="employee-update content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

    <div class="employee-form">

        <?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>
        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'Employee'),
            'content' => $this->render('_form', ['modelForm' => $modelForm, 'form' => $form]),
        ]) ?>
        <?php ActiveForm::end(); ?>

        <?= GridView::widget([
            'id' => 'EmployeeHistoryBuildGrid',
            'dataProvider' => $dataProviderEmployeeHB,
            'filterModel' => $searchModelEmployeeHB,
            'columns' => [
                'build.build_name',
                [
                    'attribute' => 'employee_history_build_deactive',
                    'format' => 'date',
                ],
            ],
            'crudSettings' => [
                'create' => ['configuration/employee-history-build/create', 'employee' => Yii::$app->request->get('id')],
                'update' => 'configuration/employee-history-build/update',
                'delete' => 'configuration/employee-history-build/delete',
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_HOME),
                'title' => Yii::t('common/employee', 'Builds'),
            ],
        ]);
        ?>

        <div class="form-group toolbox-form-group">
            <?= Html::updateButton(['form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
