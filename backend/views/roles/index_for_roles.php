<?php

use common\widgets\GridView\GridView;
use common\widgets\GridView\services\GWFilterDialogConfig;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $filterModel \domain\models\base\filter\AuthItemFilter */
/* @var $dataProvider yii\data\ActiveDataProvider */

echo GridView::widget([
    'id' => 'test1',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterDialog' => GWFilterDialogConfig::set()->filterModel($filterModel),
    'minHeight' => 510,
    'panelPrefix' => 'wkDialogGrid panel panel-',
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
            'filterType' => GridView::FILTER_DATE_RANGE,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'locale' => [
                        'format' => 'DD.MM.YYYY',
                    ],
                ],
            ],
        ],
    ],
    'panelHeading' => ['icon' => FA::icon(FA::_LIST_ALT),
        'title' => Yii::t('common/roles', 'Roles'),
    ],
    'pjaxSettings' => [
        'options' => [
            'clientOptions' => [
                'url' => new yii\web\JsExpression('function() { return (typeof event.srcElement == "undefined") ? "' . \yii\helpers\Url::to([Yii::$app->urlManager->parseRequest(Yii::$app->request)[0]]) . '" : event.srcElement.href }'),
            ],
            'enablePushState' => false,
        ],
    ],
]);