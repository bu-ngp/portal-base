<?php

use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\BuildSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ngp/ofoms', 'Ofoms search');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ofoms-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
