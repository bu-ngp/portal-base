<?php

use common\widgets\Html\Html;
use ngp\assets\OfomsAsset;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm \ngp\services\forms\OfomsAttachListForm */

$this->title = Yii::t('ngp/ofoms', 'Attach with list');
?>
    <div class="ofoms-attach-list-update content-container">

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

            <div class="form-group toolbox-form-group">
                <?= Html::updateButton(['form' => 'test'], Yii::t('ngp/ofoms', 'Attach')) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

<?php OfomsAsset::register($this) ?>