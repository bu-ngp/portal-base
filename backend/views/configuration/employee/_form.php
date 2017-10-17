<?php

use common\widgets\ActiveForm\ActiveForm;
use domain\models\base\Dolzh;
use common\widgets\Select2\Select2;
use domain\models\base\Podraz;
use rmrevin\yii\fontawesome\FA;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeForm */
?>

<?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>

<?= $form->field($modelForm, 'dolzh_id')->select2([
    'activeRecordClass' => Dolzh::className(),
    // 'queryCallback' => \domain\queries\DolzhQuery::getCallbackAllDolzhs(),
    'queryCallback' => function (ActiveQuery $query) {
        $query->select(['dolzh_id', 'dolzh_name']);
    },
    'ajaxConfig' => [
        'searchAjaxCallback' => function (ActiveQuery $query, $searchString) {
            $query->andWhere(['like', 'dolzh_name', $searchString]);
        },
        //'onlyAjax' => true,
    ],
    'wkkeep' => true,
    'wkicon' => FA::_ADDRESS_BOOK,
    'multiple' => false,
    'selectionGridUrl' => ['configuration/spravochniki/dolzh/index'],
]); ?>


<?= $form->field($modelForm, 'podraz_id')->select2([
    'activeRecordClass' => Podraz::className(),
    'queryCallback' => function (ActiveQuery $query) {
        $query->select(['podraz_id', 'podraz_name']);
    },
    'ajaxConfig' => [
        'searchAjaxCallback' => function (ActiveQuery $query, $searchString) {
            $query->andWhere(['like', 'podraz_name', $searchString]);
        },
    ],
    'wkkeep' => true,
    'wkicon' => FA::_ADDRESS_BOOK,
    'multiple' => false,
    'selectionGridUrl' => ['configuration/spravochniki/podraz/index'],
]); ?>

<?= $form->field($modelForm, 'employee_begin')->datetime(['wkkeep' => true, 'wkicon' => FA::_CALENDAR]) ?>

<?php ActiveForm::end(); ?>