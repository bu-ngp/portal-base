<?php

use common\widgets\ActiveForm\ActiveForm;
use ngp\assets\OfomsAsset;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\BuildSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ngp/ofoms', 'Ofoms search');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="ofoms-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="form-group pmd-textfield pmd-textfield-floating-label">
            <?= Html::label(Yii::t('ngp/ofoms', 'Search'), 'ofoms_search') ?>
            <?= Html::input('text', 'OfomsSearch[search_string]', $searchModel->search_string, ['id' => 'ofoms_search', 'class' => 'form-control']) ?>
            <p class="help-block"><?= Yii::t('ngp/ofoms', 'Type to search input and press Enter button') ?></p>
        </div>

        <?= GridView::widget([
            'id' => 'ofomsGrid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'minHeight' => 400,
            'columns' => [
                'fam',
                'im',
                'ot',
                'dr',
                [
                    'attribute' => 'att_doct_amb',
                ],
                [
                    'attribute' => 'att_lpu_amb',
                ],
                [
                    'attribute' => 'att_lpu_stm',
                    'visible' => false,
                ],
                [
                    'attribute' => 'dt_att_stm',
                    'visible' => false,
                ],
                [
                    'attribute' => 'w',
                    'visible' => false,
                ],
                'enp',
                [
                    'attribute' => 'opdoc',
                    'visible' => false,
                ],
                'polis',
                'spol',
                'npol',
                'dbeg',
                'dend',
                [
                    'attribute' => 'q',
                    'visible' => false,
                ],
                [
                    'attribute' => 'q_name',
                    'visible' => false,
                ],
                'rstop',
                'ter_st',
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_HOME),
                'title' => Yii::t('ngp/ofoms', 'Ofoms search'),
            ],
        ]) ?>

    </div>
<?php OfomsAsset::register($this) ?>