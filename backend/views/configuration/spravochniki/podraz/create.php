<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\Html\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\PodrazForm */

$this->title = Yii::t('common/podraz', 'Create Podraz');
?>
<div class="podraz-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="podraz-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'podraz_name')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::createButton() ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>