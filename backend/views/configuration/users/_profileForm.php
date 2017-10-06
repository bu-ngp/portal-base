<?php

use common\widgets\ActiveForm\ActiveForm;
use domain\models\base\Profile;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $modelProfileForm domain\forms\base\UserForm */
?>

<?php //$profileForm = ActiveForm::begin(['id' => $modelProfileForm->formName()]); ?>

    <div class="form-group">
        <div class="row">
            <div class="col-xs-6">
                <?= $profileForm->field($modelProfileForm, 'profile_dr')->datetime(['wkkeep' => true, 'wkicon' => FA::_CALENDAR]) ?>
            </div>
            <div class="col-xs-6">
                <?= $profileForm->field($modelProfileForm, 'profile_pol')
                    ->radioList([
                        Profile::MALE => '<i style="color: #555" class="fa fa-2x fa-' . FA::_MALE . '"></i>',
                        Profile::FEMALE => '<i style="color: #555" class="fa fa-2x fa-' . FA::_FEMALE . '"></i>'
                    ], [
                        'wkkeep' => true,
                        'encode' => false,
                        'titles' => Profile::items()['profile_pol']
                    ])
                    ->inline()
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-xs-6">
                <?= $profileForm->field($modelProfileForm, 'profile_inn')->maskedInput(['mask' => '999999999999', 'wkkeep' => true, 'wkicon' => FA::_ADDRESS_BOOK_O]) ?>
            </div>
            <div class="col-xs-6">
                <?= $profileForm->field($modelProfileForm, 'profile_snils')->snilsInput(['wkkeep' => true, 'wkicon' => FA::_COPYRIGHT]) ?>
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

<?php //ActiveForm::end(); ?>