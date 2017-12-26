<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\PodrazForm */

$this->title = $modelForm->podraz_name;
?>
<div class="podraz-update content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

    <div class="podraz-form">

        <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($modelForm, 'podraz_name')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::updateButton() ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>