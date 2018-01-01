<?php

use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;
use console\helpers\RbacHelper;
use domain\forms\base\RoleUpdateForm;
use domain\models\base\AuthItem;
use domain\models\base\AuthItemChild;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $testForm \domain\forms\AcceptanceTestForm */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Приемочные тесты';
?>
<div class="acceptance-test content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>
    <?= \common\widgets\Panel\Panel::widget([
        'label' => 'Select2',
        'content' => $this->render('_select2', [
            'testForm' => $testForm,
        ])])
    ?>
</div>

