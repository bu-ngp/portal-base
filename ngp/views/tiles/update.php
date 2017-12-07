<?php

use common\widgets\Html\Html;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use ngp\assets\JcropAsset;
use ngp\assets\TilesAsset;
use ngp\services\models\Tiles;
use common\widgets\ActiveForm\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $modelForm ngp\services\forms\TilesForm */

$this->title = Yii::t('ngp/tiles', 'Update Tiles');
?>
<div class="tiles-update content-container">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="tiles-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('ngp/tiles', 'Picture'),
                    'content' => Panel::widget([
                        'label' => Yii::t('ngp/tiles', 'Preview'),
                        'content' => $this->render('_picture', ['form' => $form, 'modelForm' => $modelForm]),
                    ]),
                ],
                [
                    'label' => Yii::t('ngp/tiles', 'Icon'),
                    'content' => Panel::widget([
                        'label' => Yii::t('ngp/tiles', 'Preview'),
                        'content' => $this->render('_icon', ['form' => $form, 'modelForm' => $modelForm]),
                    ]),
                ],
            ],
        ]) ?>

        <?= $form->field($modelForm, 'tiles_thumbnail_x')->hiddenInput()->label(false) ?>
        <?= $form->field($modelForm, 'tiles_thumbnail_x2')->hiddenInput()->label(false) ?>
        <?= $form->field($modelForm, 'tiles_thumbnail_y')->hiddenInput()->label(false) ?>
        <?= $form->field($modelForm, 'tiles_thumbnail_y2')->hiddenInput()->label(false) ?>
        <?= $form->field($modelForm, 'tiles_thumbnail_w')->hiddenInput()->label(false) ?>
        <?= $form->field($modelForm, 'tiles_thumbnail_h')->hiddenInput()->label(false) ?>

        <?= $form->field($modelForm, 'tiles_icon_color')->select2([
            'data' => Tiles::items()['tiles_icon_color'],
            'hideSearch' => true,
            'wkkeep' => true,
            // 'wkicon' => FA::_WINDOW_RESTORE,
        ]) ?>

        <?= $form->field($modelForm, 'tiles_name')->textInput(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'tiles_description')->textarea(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'tiles_link')->textInput(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'tiles_keywords')->textInput(['wkkeep' => true]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::updateButton() ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php Modal::begin([
    'id' => 'cropper-dialog',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h2 class="pmd-card-title-text">' . Yii::t('ngp/tiles', 'Cropper dialog') . '</h2>',
    'footer' => '<button data-dismiss="modal" class="btn pmd-btn-flat pmd-ripple-effect btn-default" type="button">' . Yii::t('ngp/tiles', 'Close') . '</button>',
    'footerOptions' => [
        'class' => 'pmd-modal-action pmd-modal-bordered text-right',
    ],
]) ?>

    <div class="row">
        <div class="col-md-12">
            <div class="wk-tiles-crop-wrap">
                <img class="wk-tiles-crop">
            </div>
        </div>
    </div>

<?php Modal::end() ?>
<?php JcropAsset::register($this) ?>
<?php TilesAsset::register($this) ?>