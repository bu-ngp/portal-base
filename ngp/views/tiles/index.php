<?php

use yii\bootstrap\Html;
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
            [
                'attribute' => 'tiles_thumbnail',
                'format' => 'html',
                'value' => function ($model) {
                    preg_match('/(.*)-\d+x\d+(\.\w+)$/', $model->tiles_thumbnail, $thumb);
                    return Html::img($thumb[1] . '-145x85' . $thumb[2]);
                }
            ],
            'tiles_name',
            'tiles_description',
            [
                'attribute' => 'tiles_link',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->tiles_link, 'http://'.$model->tiles_link, ['target' => '_blank', 'data-pjax' => '0']);
                },
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
