<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeHistoryForm */
/* @var $searchModelBuild domain\models\base\search\BuildSearch */
/* @var $dataProviderBuild yii\data\ActiveDataProvider */

$this->title = Yii::t('common/person', 'Create Employee');
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'Employee'),
            'content' => $this->render('_form', ['modelForm' => $modelForm]),
        ]) ?>

        <?= ''
//        GridView::widget([
//            'id' => 'EmployeeHistoryBuildCreateGrid',
//            'dataProvider' => $dataProviderBuild,
//            'filterModel' => $searchModelBuild,
//            'columns' => [
//                'build_name',
//            ],
//            'crudSettings' => [
//                'create' => [
//                    'urlGrid' => 'configuration/spravochniki/build/index',
//                    'inputName' => 'EmployeeHistoryForm[assignBuilds]',
//                ],
//                'delete' => [
//                    'inputName' => 'EmployeeHistoryForm[assignBuilds]',
//                ],
//            ],
//            'panelHeading' => [
//                'icon' => FA::icon(FA::_LIST_ALT),
//                'title' => Yii::t('domain\employee_history_build', 'Builds'),
//            ],
//        ]);
        ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Next'), ['class' => 'btn btn-success', 'form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
