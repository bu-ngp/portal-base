<?php


use common\widgets\ActiveForm\ActiveForm;
use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;
use common\widgets\Panel\Panel;

/* @var $this yii\web\View */
/* @var $modelForm \domain\forms\base\ConfigCommonUpdateForm */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/config-common', 'Common Settings');
?>
<div class="config-common-update content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

    <div class="config-common-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= Panel::widget([
            'label' => Yii::t('common/config-common', 'Mail Config'),
            'content' => $this->render('_mail_config', ['form' => $form, 'modelForm' => $modelForm])
        ]) ?>

        <?= Panel::widget([
            'label' => Yii::t('common/config-common', 'Footer Config'),
            'content' => $this->render('_footer_config', ['form' => $form, 'modelForm' => $modelForm])
        ]) ?>

        <?= Panel::widget([
            'label' => Yii::t('common/config-common', 'Theme Config'),
            'content' => $form->field($modelForm, 'config_common_christmas')->toggleSwitch(),
        ]) ?>

        <?= Panel::widget([
            'label' => Yii::t('common/config-common', 'Import Data Config'),
            'content' => $form->field($modelForm, 'config_common_import_employee')->toggleSwitch(),
        ]) ?>

        <div class="form-group toolbox-form-group">
            <?= Html::updateButton() ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>