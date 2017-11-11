<?php

use yii\bootstrap\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm ngp\services\forms\TilesForm */

$this->title = Yii::t('ngp/tiles', 'Update "{modelClass}": ', [
    'modelClass' => $modelForm->tiles_name,
]);
?>
<div class="tiles-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="tiles-form">

        <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($modelForm, 'tiles_name')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

<?= $form->field($modelForm, 'tiles_description')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

<?= $form->field($modelForm, 'tiles_link')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

<?= $form->field($modelForm, 'tiles_thumbnail')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

<?= $form->field($modelForm, 'tiles_icon')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

<?= $form->field($modelForm, 'tiles_icon_color')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

<?= $form->field($modelForm, 'created_at')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

<?= $form->field($modelForm, 'updated_at')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

<?= $form->field($modelForm, 'created_by')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

<?= $form->field($modelForm, 'updated_by')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>