<?php

use ngp\assets\OfomsAsset;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm \ngp\services\forms\OfomsAttachListForm */

$this->title = Yii::t('ngp/ofoms', 'Attach with list');
?>
    <div class="ofoms-attach-list-update">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="ofoms-attach-list-form">
            <?php $form = ActiveForm::begin(['id' => 'test', 'options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="row">
                <div class="col-md-12">
                    <?= \common\widgets\Panel\Panel::widget([
                        'label' => Yii::t('ngp/ofoms', 'Upload List'),
                        'content' => $this->render('_attach-list-panel', ['form' => $form, 'modelForm' => $modelForm]),
                    ]) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('ngp/ofoms', 'Attach'), ['class' => 'btn btn-primary', 'form' => 'test']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

<?php OfomsAsset::register($this) ?>