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
/* @var $searchModelChooseBuild \domain\models\base\search\BuildSearch */
/* @var $dataProviderChooseBuild yii\data\ActiveDataProvider */
/* @var $searchModelAuthitem \domain\models\base\search\AuthItemSearch */
/* @var $dataProviderAuthitem yii\data\ActiveDataProvider */
/* @var $filterModelAuthitem \domain\models\base\filter\AuthItemTestFilter */

$this->title = 'Приемочные тесты';
?>
<div class="acceptance-test content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

    <?= \common\widgets\Tabs\Tabs::widget([
        'items' => [
            [
                'label' => 'Элементы форм',
                'content' => $this->render('_test_forms', ['testForm' => $testForm]),
            ],
            [
                'label' => 'Грид выбранных значений',
                'content' => $this->render('_test_choose_grid', [
                    'testForm' => $testForm,
                    'searchModelChooseBuild' => $searchModelChooseBuild,
                    'dataProviderChooseBuild' => $dataProviderChooseBuild,
                ]),
            ],
            [
                'label' => 'Грид с полным функционалом',
                'content' => $this->render('_test_grid', [
                    'testForm' => $testForm,
                    'searchModelAuthitem' => $searchModelAuthitem,
                    'dataProviderAuthitem' => $dataProviderAuthitem,
                    'filterModelAuthitem' => $filterModelAuthitem,
                ]),
            ],
        ],
    ]) ?>

</div>

