<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\PodrazForm */

$this->title = Yii::t('common/podraz', 'Create Podraz');
?>
<div class="podraz-create content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

    <div class="podraz-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'podraz_name')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::createButton() ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>