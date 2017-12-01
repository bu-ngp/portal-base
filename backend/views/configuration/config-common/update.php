<?php

use yii\helpers\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm \domain\forms\base\ConfigCommonUpdateForm */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/config-common', 'Common Settings');
?>
<div class="config-common-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="config-common-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= \common\widgets\Panel\Panel::widget([
            'label' => Yii::t('common/config-common', 'Mail Config'),
            'content' => $this->render('_mail_config', ['form' => $form, 'modelForm' => $modelForm])
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>