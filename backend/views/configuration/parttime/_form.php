<?php

use common\widgets\ActiveForm\ActiveForm;
use domain\models\base\Dolzh;
use domain\models\base\Podraz;
use rmrevin\yii\fontawesome\FA;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\ParttimeForm */
/* @var $form ActiveForm */
?>

<?= $form->field($modelForm, 'dolzh_id')->select2([
    'activeRecordClass' => Dolzh::className(),
    'queryCallback' => function (ActiveQuery $query) {
        $query->select(['dolzh_id', 'dolzh_name']);
    },
    'ajaxConfig' => [
        'searchAjaxCallback' => function (ActiveQuery $query, $searchString) {
            $query->andWhere(['like', 'dolzh_name', $searchString]);
        },
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

<?= $form->field($modelForm, 'parttime_begin')->datetime(['wkkeep' => true, 'wkicon' => FA::_CALENDAR]) ?>

<?= $form->field($modelForm, 'parttime_end')->datetime(['wkkeep' => true, 'wkicon' => FA::_CALENDAR]) ?>