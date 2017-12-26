<?php

use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;
use common\widgets\Panel\Panel;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeBuildForm */

$this->title = Yii::t('common/employee', 'Add Build');
?>
<div class="parttime-build-create content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

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
