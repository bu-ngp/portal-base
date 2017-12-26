<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\DolzhForm */

$this->title = $modelForm->dolzh_name;
?>
<div class="dolzh-update content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

    <div class="dolzh-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'dolzh_name')->textInput(['maxlength' => true, 'wkkeep' => true]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::updateButton() ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>