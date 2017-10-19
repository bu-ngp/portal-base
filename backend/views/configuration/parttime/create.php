<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\ParttimeForm */
/* @var $searchModelBuild domain\models\base\search\BuildSearch */
/* @var $dataProviderBuild yii\data\ActiveDataProvider */

$this->title = Yii::t('common/person', 'Create Parttime');
?>
<div class="parttime-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="parttime-form">

        <?= Panel::widget([
            'label' => Yii::t('common/parttime', 'Parttime'),
            'content' => $this->render('_form', ['modelForm' => $modelForm]),
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Next'), ['class' => 'btn btn-success', 'form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
