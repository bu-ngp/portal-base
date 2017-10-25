<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\GridView\GridView;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeBuildForm */

$this->title = Yii::t('common/employee', 'Add Build');
?>
<div class="parttime-build-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="parttime-build-form">

        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'Builds'),
            'content' => $this->render('_form', ['modelForm' => $modelForm]),
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Create'), ['class' => 'btn btn-success', 'form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
