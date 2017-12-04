<?php

use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelForm \domain\forms\base\ConfigCommonUpdateForm */
/* @var $form \common\widgets\ActiveForm\ActiveForm */
?>

<?= $form->field($modelForm, 'config_common_footer_company')->textInput(['wkkeep' => true, 'wkicon' => FA::_COPYRIGHT]) ?>

<?= $form->field($modelForm, 'config_common_footer_addition')->textInput(['wkkeep' => true, 'wkicon' => FA::_INFO]) ?>