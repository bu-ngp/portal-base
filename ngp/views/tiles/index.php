<?php

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\CardList\CardList;
use ngp\assets\TilesAsset;
use yii\bootstrap\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;

/* @var $this yii\web\View */
/* @var $searchModel ngp\services\models\search\TilesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

Breadcrumbs::root();
$this->title = Yii::t('ngp/tiles', 'Tiles');
?>
<div class="tiles-index content-container">

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
                    $color = $model->tiles_icon_color ?: CardList::GREY_STYLE;
                    if ($model->tiles_thumbnail) {
                        preg_match('/(.*)-\d+x\d+(\.\w+)$/', $model->tiles_thumbnail, $thumb);
                        return Html::tag('div', Html::img($thumb[1] . '-165x95' . $thumb[2]), ['class' => "wk-tiles-preview-grid $color"]);
                    } else {
                        $icon = '<i class="' . $model->tiles_icon . '"></i>' ?: FA::icon(FA::_WINDOW_MAXIMIZE);
                        return Html::tag('div', $icon, ['class' => "wk-tiles-preview-grid $color"]);
                    }
                }
            ],
            [
                'attribute' => 'tiles_name',
                'noWrap' => false,
            ],
            [
                'attribute' => 'tiles_description',
                'noWrap' => false,
            ],
            [
                'attribute' => 'tiles_link',
                'format' => 'raw',
                'noWrap' => false,
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
