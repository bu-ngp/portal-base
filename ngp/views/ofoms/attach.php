<?php

use domain\models\base\Person;
use ngp\services\queries\VrachQuery;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm \ngp\services\forms\OfomsAttachForm */

$this->title = $modelForm->fam . ' ' . $modelForm->im . ' ' . $modelForm->ot;
?>
<div class="ofoms-attach">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="ofoms-attach-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'enp')->textInput(['wkkeep' => true, 'maxlength' => true, 'disabled' => true]) ?>
        <?= $form->field($modelForm, 'fam')->textInput(['wkkeep' => true, 'maxlength' => true, 'disabled' => true]) ?>
        <?= $form->field($modelForm, 'im')->textInput(['wkkeep' => true, 'maxlength' => true, 'disabled' => true]) ?>
        <?= $form->field($modelForm, 'ot')->textInput(['wkkeep' => true, 'maxlength' => true, 'disabled' => true]) ?>
        <?= $form->field($modelForm, 'dr')->textInput(['wkkeep' => true, 'maxlength' => true, 'disabled' => true]) ?>
        <?= $form->field($modelForm, 'vrach_inn')->select2([
            'activeRecordClass' => Person::className(),
            'activeRecordAttribute' => 'person_id',
            'queryCallback' => VrachQuery::select(),
            'ajaxConfig' => [
                'searchAjaxCallback' => VrachQuery::search(),
            ],
            'wkkeep' => true,
            'wkicon' => FA::_STETHOSCOPE,
            'selectionGridUrl' => Yii::$app->get('urlManagerAdmin')->createUrl(['users/index']),
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('ngp/ofoms', 'Attach'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>