<?php

use common\widgets\ActiveForm\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\DolzhForm */

$this->title = Yii::t('common/dolzh', 'Update "{modelClass}": ', [
    'modelClass' => $modelForm->dolzh_name,
]);
?>
<div class="dolzh-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="dolzh-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'dolzh_name')->textInput(['maxlength' => true, 'wkkeep' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common/dolzh', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>