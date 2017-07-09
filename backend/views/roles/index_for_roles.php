<?php

use common\widgets\GridView\services\GWFilterDialogConfig;
use common\widgets\GridViewModal\GridViewModal;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $filterModel \domain\models\base\filter\AuthItemFilter */
/* @var $dataProvider yii\data\ActiveDataProvider */

echo GridViewModal::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterDialog' => GWFilterDialogConfig::set()->filterModel($filterModel),
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
    'panelHeading' => ['icon' => FA::icon(FA::_LIST_ALT),
        'title' => Yii::t('common/roles', 'Roles'),
    ],
]);