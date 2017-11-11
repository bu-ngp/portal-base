<?php

use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use ngp\assets\JcropAsset;
use ngp\assets\TilesAsset;
use yii\bootstrap\Html;
use common\widgets\ActiveForm\ActiveForm;
use yii\bootstrap\Modal;
use budyaga\cropper\Widget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $modelForm ngp\services\forms\TilesForm */

$this->title = Yii::t('ngp/tiles', 'Create Tiles');
?>
<div class="tiles-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="tiles-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('ngp/tiles', 'Picture'),
                    'content' => Panel::widget([
                        'label' => 'Config',
                        'content' => $this->render('_picture', ['form' => $form, 'modelForm' => $modelForm]),
                    ]),
                ],
                [
                    'label' => Yii::t('ngp/tiles', 'Icon'),
                    'content' => $this->render('_icon'),
                ],
            ],
        ]) ?>

        <?= $form->field($modelForm, 'tiles_thumbnail_x')->hiddenInput()->label(false) ?>
        <?= $form->field($modelForm, 'tiles_thumbnail_x2')->hiddenInput()->label(false) ?>
        <?= $form->field($modelForm, 'tiles_thumbnail_y')->hiddenInput()->label(false) ?>
        <?= $form->field($modelForm, 'tiles_thumbnail_y2')->hiddenInput()->label(false) ?>

        <?= $form->field($modelForm, 'tiles_name')->textInput(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'tiles_description')->textarea(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'tiles_link')->textInput(['wkkeep' => true]) ?>

        <?= $form->field($modelForm, 'tiles_keywords')->textInput(['wkkeep' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Create'), ['class' => 'btn btn-success']) ?>
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