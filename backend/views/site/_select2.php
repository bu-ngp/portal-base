<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 31.12.2017
 * Time: 17:49
 */

use common\widgets\ActiveForm\ActiveForm;
use rmrevin\yii\fontawesome\FA;

class TestForm extends \yii\base\Model {
    public $dolzh_id;
    public $dolzh_multiple_id;
}
$dolzhModel = new TestForm();


/* @var $dolzhModel \domain\models\base\Dolzh */
/* @var $dolzhModelMultiple \domain\models\base\Dolzh */
/* @var $podrazModel \domain\models\base\Podraz */
?>

<?php $form = ActiveForm::begin([
        'id' => 'one',
]); ?>

    <div class="form-group">
        <div class="row">
            <div class="col-xs-6">

                <?= $form->field($dolzhModel, 'dolzh_id',['inputOptions' => ['name' => 'test']])->select2([
                    'activeRecordClass' => \domain\models\base\Dolzh::className(),
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

                <?= $form->field($dolzhModel, 'dolzh_multiple_id')->select2([
                    'activeRecordClass' => \domain\models\base\Dolzh::className(),
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

<?php ActiveForm::end(); ?>