<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 31.12.2017
 * Time: 17:49
 */

use common\widgets\ActiveForm\ActiveForm;
use rmrevin\yii\fontawesome\FA;

/* @var $testForm \domain\forms\AcceptanceTestForm */
?>

<?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <div class="row">
            <div class="col-xs-6">

                <?= $form->field($testForm, 'dolzh_single_id')->select2([
                    'activeRecordClass' => \domain\models\base\Dolzh::className(),
                    'activeRecordAttribute' => 'dolzh_id',
                    'queryCallback' => \domain\queries\DolzhQuery::select(),
                    'ajaxConfig' => [
                        'searchAjaxCallback' => \domain\queries\DolzhQuery::search(),
                    ],
                    'wkkeep' => true,
                    'wkicon' => FA::_WINDOW_RESTORE,
                    'selectionGridUrl' => ['configuration/spravochniki/dolzh/index'],
                ]) ?>

            </div>
            <div class="col-xs-6">

                <?= $form->field($testForm, 'dolzh_multiple_id')->select2([
                    'activeRecordClass' => \domain\models\base\Dolzh::className(),
                    'activeRecordAttribute' => 'dolzh_id',
                    'queryCallback' => \domain\queries\DolzhQuery::select(),
                    'ajaxConfig' => [
                        'searchAjaxCallback' => \domain\queries\DolzhQuery::search(),
                    ],
                    'wkkeep' => true,
                    'multiple' => true,
                    'wkicon' => FA::_WINDOW_RESTORE,
                    'selectionGridUrl' => ['configuration/spravochniki/dolzh/index'],
                ]) ?>

            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-xs-6">

                <?= $form->field($testForm, 'podraz_single_id')->select2([
                    'activeRecordClass' => \domain\models\base\Podraz::className(),
                    'activeRecordAttribute' => 'podraz_id',
                    'queryCallback' => \domain\queries\PodrazQuery::select(),
                    'ajaxConfig' => [
                        'searchAjaxCallback' => \domain\queries\PodrazQuery::search(),
                    ],
                    'wkkeep' => true,
                    'wkicon' => FA::_WINDOW_RESTORE,
                    'selectionGridUrl' => ['configuration/spravochniki/podraz/index'],
                ]) ?>

            </div>
            <div class="col-xs-6">

                <?= $form->field($testForm, 'podraz_multiple_id')->select2([
                    'activeRecordClass' => \domain\models\base\Podraz::className(),
                    'activeRecordAttribute' => 'podraz_id',
                    'queryCallback' => \domain\queries\PodrazQuery::select(),
                    'ajaxConfig' => [
                        'searchAjaxCallback' => \domain\queries\PodrazQuery::search(),
                    ],
                    'wkkeep' => true,
                    'multiple' => true,
                    'wkicon' => FA::_WINDOW_RESTORE,
                    'selectionGridUrl' => ['configuration/spravochniki/podraz/index'],
                ]) ?>

            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>