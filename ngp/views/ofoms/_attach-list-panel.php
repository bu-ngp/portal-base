<?php

use dosamigos\fileinput\FileInput;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $modelForm \ngp\services\forms\OfomsAttachListForm */
/* @var $form \common\widgets\ActiveForm\ActiveForm */
?>

<?= $form->field($modelForm, 'listFile')->widget(FileInput::className(), [
    'style' => FileInput::STYLE_BUTTON
])->label(false) ?>