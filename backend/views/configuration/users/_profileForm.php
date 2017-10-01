<?php

use common\widgets\ActiveForm\ActiveForm;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelProfileForm domain\forms\base\UserForm */
?>

<?php $profileForm = ActiveForm::begin(['id' => $modelProfileForm->formName()]); ?>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $profileForm->field($modelProfileForm, 'profile_dr')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_CALENDAR]) ?>
        </div>
        <div class="col-xs-6">
            <?= $profileForm->field($modelProfileForm, 'profile_pol')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_INTERSEX]) ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $profileForm->field($modelProfileForm, 'profile_inn')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_ADDRESS_BOOK_O]) ?>
        </div>
        <div class="col-xs-6">
            <?= $profileForm->field($modelProfileForm, 'profile_snils')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_COPYRIGHT]) ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $profileForm->field($modelProfileForm, 'profile_address')->textInput(['wkkeep' => true, 'maxlength' => true, 'wkicon' => FA::_HOME]) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>