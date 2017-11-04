<?php

use doh\assets\DoHAsset;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;

/* @var $this yii\web\View */
/* @var $searchModel doh\services\models\search\HandlerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('doh', 'Handlers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="handler-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'handler_at',
                'format' => 'datetime',

            ],
            [
                'attribute' => 'handler_description',
                'filter' => false,
            ],
            'handler_status',
            [
                'attribute' => 'handler_percent',
                'filter' => false,
            ],
            [
                'attribute' => 'handler_short_report',
                'filter' => false,
            ],
            [
                'attribute' => 'handler_files',
                'filter' => false,
            ],
            [
                'attribute' => 'handler_done_time',
                'filter' => false,
                'format' => 'time',
                'visible' => false,
            ],
            [
                'attribute' => 'handler_short_report',
                'filter' => false,
                'visible' => false,
            ],
            [
                'attribute' => 'handler_id',
                'filter' => false,
                'visible' => false,
            ],
            [
                'attribute' => 'identifier',
                'filter' => false,
                'visible' => false,
            ],
            [
                'attribute' => 'handler_name',
                'filter' => false,
                'visible' => false,
            ],
        ],
        'panelHeading' => [
            'icon' => FA::icon(FA::_LIST_ALT),
            'title' => Yii::t('doh', 'Handlers'),
        ],
        'leftBottomToolbar' => \yii\bootstrap\Html::button('test',['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary','id'=>'test1']),
    ]) ?>

</div>

<?php DoHAsset::register($this) ?>