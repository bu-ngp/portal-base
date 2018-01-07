<?php
/* @var $this yii\web\View */
/* @var $testForm \domain\forms\AcceptanceTestForm */
?>

<?= \common\widgets\Panel\Panel::widget([
    'label' => 'Select2',
    'content' => $this->render('_select2', [
        'testForm' => $testForm,
    ])]) ?>