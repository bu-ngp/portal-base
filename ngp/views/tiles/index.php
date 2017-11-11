<?php

use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;

/* @var $this yii\web\View */
/* @var $searchModel ngp\services\models\search\TilesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ngp/tiles', 'Tiles');
?>
<div class="tiles-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //'preview',
            'tiles_name',
            'tiles_description',
            [
                'attribute' => 'tiles_link',
            ],
            [
                'attribute' => 'tiles_keywords',
                'visible' => false,
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'visible' => false,
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'visible' => false,
            ],
            [
                'attribute' => 'created_by',
                'visible' => false,
            ],
            [
                'attribute' => 'updated_by',
                'visible' => false,
            ],
        ],
        'crudSettings' => [
            'create' => 'tiles/create',
            'update' => 'tiles/update',
            'delete' => 'tiles/delete',
        ],
        'panelHeading' => [
            'icon' => FA::icon(FA::_BARS),
            'title' => Yii::t('ngp/tiles', 'Tiles'),
        ],
    ]); ?>

</div>
