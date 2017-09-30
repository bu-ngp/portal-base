<?php

use yii\helpers\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\DolzhForm */

$this->title = Yii::t('common/dolzh', 'Create Dolzh');
?>
<div class="dolzh-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="dolzh-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelForm, 'dolzh_name')->textInput(['maxlength' => true, 'wkkeep' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common/dolzh', 'Create'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>