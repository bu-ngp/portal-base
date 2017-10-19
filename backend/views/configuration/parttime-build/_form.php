<?php

use common\widgets\ActiveForm\ActiveForm;
use domain\models\base\Build;
use domain\models\base\Dolzh;
use common\widgets\Select2\Select2;
use domain\models\base\Podraz;
use rmrevin\yii\fontawesome\FA;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\ParttimeBuildForm */
?>

<?php $form = ActiveForm::begin(['id' => $modelForm->formName()]); ?>

<?= $form->field($modelForm, 'build_id')->select2([
    'activeRecordClass' => Build::className(),
    'queryCallback' => function (ActiveQuery $query) {
        $query->select(['build_id', 'build_name']);
    },
    'ajaxConfig' => [
        'searchAjaxCallback' => function (ActiveQuery $query, $searchString) {
            $query->andWhere(['like', 'build_name', $searchString]);
        },
    ],
    'wkkeep' => true,
    'wkicon' => FA::_ADDRESS_BOOK,
    'multiple' => false,
    'selectionGridUrl' => ['configuration/spravochniki/build/index'],
]); ?>

<?= $form->field($modelForm, 'parttime_build_deactive')->datetime(['wkkeep' => true, 'wkicon' => FA::_CALENDAR]) ?>

<?php ActiveForm::end(); ?>