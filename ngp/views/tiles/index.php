<?php

use common\widgets\CardList\CardList;
use ngp\assets\TilesAsset;
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
                'filter' => false,
                'value' => function ($model) {
                    if ($model->tiles_thumbnail) {
                        preg_match('/(.*)-\d+x\d+(\.\w+)$/', $model->tiles_thumbnail, $thumb);
                        return Html::img($thumb[1] . '-165x95' . $thumb[2]);
                    } else {
                        $icon = '<i class="' . $model->tiles_icon . '"></i>' ?: FA::icon(FA::_WINDOW_MAXIMIZE);
                        $color = $model->tiles_icon_color ?: CardList::GREY_STYLE;
                        return Html::tag('div', $icon, ['class' => "wk-tiles-preview-grid $color"]);
                    }
                }
            ],
            'tiles_name',
            'tiles_description',
            [
                'attribute' => 'tiles_link',
                'format' => 'raw',
                'value' => function ($model) {
                    $link = preg_match('/^(https:\/\/)|(http:\/\/)/', $model->tiles_link) ? $model->tiles_link : ('http://' . $model->tiles_link);
                    return Html::a($model->tiles_link, $link, ['target' => '_blank', 'data-pjax' => '0']);
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
<?php TilesAsset::register($this) ?>
