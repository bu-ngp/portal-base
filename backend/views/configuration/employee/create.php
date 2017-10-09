<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $modelForm domain\forms\base\EmployeeForm */

$this->title = Yii::t('common/person', 'Create Employee');
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <?= Panel::widget([
            'label' => Yii::t('common/employee', 'Employee'),
            'content' => $this->render('_form', ['modelForm' => $modelForm]),
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common/employee', 'Create'), ['class' => 'btn btn-success', 'form' => $modelForm->formName()]) ?>
        </div>

    </div>
</div>
