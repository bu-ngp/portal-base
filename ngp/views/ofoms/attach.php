<?php

use common\widgets\Html\Html;
use domain\models\base\Person;
use ngp\services\queries\VrachQuery;
use rmrevin\yii\fontawesome\FA;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm \ngp\services\forms\OfomsAttachForm */

$this->title = $modelForm->fam . ' ' . $modelForm->im . ' ' . $modelForm->ot;
?>
<div class="ofoms-attach content-container">

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
            'activeRecordAttribute' => 'profile_inn',
            'exceptAttributesFromResult' => ['profile_inn'],
            'queryCallback' => VrachQuery::select(),
            'ajaxConfig' => [
                'searchAjaxCallback' => VrachQuery::search(),
            ],
            'wkkeep' => true,
            'wkicon' => FA::_STETHOSCOPE,
            'selectionGridUrl' => Yii::$app->get('urlManagerAdmin')->createUrl(['users/index']),
        ]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::updateButton([], Yii::t('ngp/ofoms', 'Attach')) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>