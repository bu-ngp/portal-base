<?php
/* @var $modelForm ngp\services\forms\TilesForm */

/* @var $form \common\widgets\ActiveForm\ActiveForm */

use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

?>
<div class="row wk-tiles-image-container">
    <div class="col-md-4">
        <div class="wk-tiles-preview">
            <div class="wk-tiles-preview-bg"></div>
            <svg width="100%" height="100%">
                <use xlink:href="#fullHex"/>
            </svg>
        </div>
    </div>
    <div class="col-md-3 btn-group-vertical">
        <?= $form->field($modelForm, 'imageFile')->fileInput(['class' => 'wk-tiles-upload-input'])->label(false) ?>
        <?= $form->field($modelForm, 'tiles_thumbnail')->hiddenInput()->label(false) ?>
        <?= Html::button(FA::icon(FA::_PICTURE_O) . Yii::t('ngp/tiles', 'Upload Image'), [
            'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary wk-tiles-upload-image-button',
        ]) ?>

        <?= Html::button(FA::icon(FA::_SCISSORS) . Yii::t('ngp/tiles', 'Crop'), [
            'data-toggle' => 'modal',
            'data-target' => '#cropper-dialog',
            'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger wk-tiles-crop-button',
            'disabled' => true,
        ]) ?>
    </div>
</div>