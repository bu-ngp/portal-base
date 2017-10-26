<?php

use common\widgets\ActiveForm\ActiveForm;
use domain\models\base\Dolzh;
use domain\models\base\Podraz;
use domain\queries\DolzhQuery;
use domain\queries\PodrazQuery;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\ParttimeForm */
/* @var $form ActiveForm */
?>

<?= $form->field($modelForm, 'dolzh_id')->select2([
    'activeRecordClass' => Dolzh::className(),
    'queryCallback' => DolzhQuery::select(),
    'ajaxConfig' => [
        'searchAjaxCallback' => DolzhQuery::search(),
    ],
    'wkkeep' => true,
    'wkicon' => FA::_USER_CIRCLE_O,
    'multiple' => false,
    'selectionGridUrl' => ['configuration/spravochniki/dolzh/index'],
]); ?>


<?= $form->field($modelForm, 'podraz_id')->select2([
    'activeRecordClass' => Podraz::className(),
    'queryCallback' => PodrazQuery::select(),
    'ajaxConfig' => [
        'searchAjaxCallback' => PodrazQuery::search(),
    ],
    'wkkeep' => true,
    'wkicon' => FA::_WINDOW_RESTORE,
    'multiple' => false,
    'selectionGridUrl' => ['configuration/spravochniki/podraz/index'],
]); ?>

<?= $form->field($modelForm, 'parttime_begin')->datetime(['wkkeep' => true, 'wkicon' => FA::_CALENDAR_PLUS_O]) ?>

<?= $form->field($modelForm, 'parttime_end')->datetime(['wkkeep' => true, 'wkicon' => FA::_CALENDAR_TIMES_O]) ?>