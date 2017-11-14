<?php
use dosamigos\fileupload\FileUpload;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $modelForm \ngp\services\forms\OfomsAttachListForm */
/* @var $form \common\widgets\ActiveForm\ActiveForm */
?>

<?= $form->field($modelForm, 'listFile')->fileInput(['class' => 'wk-ofoms-attach-list-input'])->label(false) ?>
<?= Html::button(FA::icon(FA::_PICTURE_O) . Yii::t('ngp/ofoms', 'Upload List'), [
    'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary wk-ofoms-attach-list-button',
]) ?>