<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\BuildForm */

$this->title = $modelForm->build_name;
?>
<div class="build-update content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

    <div class="build-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'build_name')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::createButton() ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>