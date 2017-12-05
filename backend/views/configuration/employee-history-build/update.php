<?php

use common\widgets\Html\Html;
use common\widgets\Panel\Panel;
use domain\models\base\Build;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeBuildForm */

$this->title = Build::findOne($modelForm->build_id)->build_name;
?>
<div class="employee-history-build-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="employee-history-build-form">

        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'Builds'),
            'content' => $this->render('_form', ['modelForm' => $modelForm]),
        ]) ?>


        <div class="form-group toolbox-form-group">
            <?= Html::updateButton(['form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
