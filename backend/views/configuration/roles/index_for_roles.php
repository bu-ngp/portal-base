<?php

use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $filterModel domain\models\base\filter\AuthItemFilter */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $gridExcludeIdsFunc Closure */

$this->title = Yii::t('common/roles', 'Roles');
?>
<div class="auth-item-index content-container">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterDialog' => [
            'filterModel' => $filterModel,
        ],
        'columns' => [
            'description',
            'name',
            [
                'attribute' => 'type',
                'visible' => true,
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
        ],
        'gridExcludeIdsFunc' => $gridExcludeIdsFunc,
        'panelHeading' => ['icon' => FA::icon(FA::_LIST_ALT),
            'title' => Yii::t('common/roles', 'Roles'),
        ],
    ]);
    ?>
</div>