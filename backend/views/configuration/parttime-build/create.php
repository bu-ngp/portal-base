<?php

use common\widgets\Html\Html;
use common\widgets\Panel\Panel;

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

        <div class="form-group toolbox-form-group">
            <?= Html::createButton(['form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
