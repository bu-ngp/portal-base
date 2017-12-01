<?php

use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelForm \domain\forms\base\ConfigCommonUpdateForm */
/* @var $form \common\widgets\ActiveForm\ActiveForm */
?>

<?= $form->field($modelForm, 'config_common_portal_mail')->textInput(['wkkeep' => true, 'wkicon' => FA::_MAIL_FORWARD]) ?>

<?= $form->field($modelForm, 'config_common_mail_administrators')->textInput(['wkkeep' => true, 'wkicon' => FA::_MAIL_FORWARD])->hint('Если несколько, перечислите через запятую') ?>