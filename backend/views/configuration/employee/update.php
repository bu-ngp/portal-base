<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeHistoryForm */
/* @var $searchModelEmployeeHB domain\models\base\search\EmployeeHistoryBuildSearch */
/* @var $dataProviderEmployeeHB yii\data\ActiveDataProvider */

$this->title = \domain\models\base\EmployeeHistory::findOne(Yii::$app->request->get('id'))->dolzh->dolzh_name;
?>
<div class="employee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="employee-form">

        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'Employee'),
            'content' => $this->render('_form', ['modelForm' => $modelForm]),
        ]) ?>

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
                'icon' => FA::icon(FA::_LIST_ALT),
                'title' => Yii::t('domain\employee_history_build', 'Builds'),
            ],
        ]);
        ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Update'), ['class' => 'btn btn-primary', 'form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
