<?php

use yii\helpers\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\BuildForm */

$this->title = Yii::t('common/build', 'Update "{modelClass}": ', [
    'modelClass' => $modelForm->build_name,
]);
?>
<div class="build-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="build-form">

        <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($modelForm, 'build_name')->textInput(['wkkeep' => true, 'maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common/build', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>