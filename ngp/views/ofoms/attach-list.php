<?php

use ngp\assets\OfomsAsset;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;
use common\widgets\ActiveForm\ActiveForm;
use dosamigos\fileupload\FileUpload;

/* @var $this yii\web\View */
/* @var $modelForm \ngp\services\forms\OfomsAttachListForm */

$this->title = Yii::t('ngp/ofoms', 'Ofoms Prik List');
?>
    <div class="ofoms-attach-list-update">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="ofoms-attach-list-form">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-12">
                    <?= \common\widgets\Panel\Panel::widget([
                        'label' => Yii::t('ngp/ofoms', 'Ofoms Prik'),
                        'content' => $this->render('_attach-list-panel', ['form' => $form, 'modelForm' => $modelForm]),
                    ]) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('ngp/ofoms', 'Attach'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

<?php OfomsAsset::register($this) ?>