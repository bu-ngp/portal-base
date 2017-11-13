<?php

use ngp\assets\OfomsAsset;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm \ngp\services\forms\OfomsAttachListForm */

$this->title = Yii::t('ngp/ofoms', 'Ofoms Prik List');
?>
<div class="ofoms-attach-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="ofoms-attach-list-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'listFile')->fileInput(['class' => 'wk-ofoms-attach-list-input'])->label(false) ?>
        <?= Html::button(FA::icon(FA::_PICTURE_O) . Yii::t('ngp/ofoms', 'Upload List'), [
            'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary wk-ofoms-attach-list-button',
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('ngp/ofoms', 'Attach'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php OfomsAsset::register($this) ?>