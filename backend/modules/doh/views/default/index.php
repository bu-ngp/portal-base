<?php

use console\helpers\RbacHelper;
use doh\assets\DoHAsset;
use doh\assets\ProgressbarAsset;
use doh\services\models\Handler;
use yii\bootstrap\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel doh\services\models\search\HandlerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('doh', 'Handlers');
$this->params['breadcrumbs'][] = $this->title;

$handler_statuses = Handler::itemsValues('handler_status');
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
                    'attribute' => 'handler_status',
                    'filter' => $handler_statuses,
                    'format' => 'raw',
                    'headerOptions' => [
                        'attribute' => 'handler_status',
                    ],
                    'value' => function ($model, $key, $index, $column) use ($handler_statuses) {
                        /** @var Handler $model */
                        $value = $model[$column->attribute];
                        return '<span key="' . $value . '" class="label label-' . $model->labelStatus($value) . '">' . (isset($value) ? Html::encode($handler_statuses[$value]) : '') . '</span>';
                    },
                ],
                'handler_description',
                [
                    'attribute' => 'handler_percent',
                    'filter' => false,
                    'format' => 'raw',
                    'value' => function ($model, $key) {
                        return '<div class="wk-progress" key="' . $key . '" percent="' . (round($model->handler_percent / 100, 2)) . '"></div>';
                    },
                ],
                [
                    'attribute' => 'handler_short_report',
                    'filter' => false,
                    'format' => 'html',
                    'value' => function ($model) {
                        return preg_replace('/\\n/', '<br>', $model->handler_short_report);
                    },
                ],
                [
                    'attribute' => 'dohFilesList',
                    'format' => 'raw',
                    'filter' => false,
                    'contentOptions' => [
                        'class' => 'wk-doh-files',
                    ],
                ],
                [
                    'attribute' => 'handler_done_time',
                    'filter' => false,
                    'format' => 'duration',
                    'visible' => false,
                ],
                [
                    'attribute' => 'handler_used_memory',
                    'format' => 'shortSize',
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
            'customActionButtons' => array_merge(
                ['cancel' => function ($url, $model) {
                    if (in_array($model->handler_status, [Handler::QUEUE, Handler::DURING])) {
                        return Html::a('<i class="fa fa-2x fa-close"></i>', Yii::$app->get('urlManagerAdmin')->createUrl(['doh/cancel', 'id' => $model->handler_id]), [
                            'title' => Yii::t('doh', 'Cancel'),
                            'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-doh-cancel',
                            'data-pjax' => '0'
                        ]);
                    }

                    return '';
                }],
                Yii::$app->getUser()->can(RbacHelper::ADMINISTRATOR) ? ['delete' => function ($url, $model) {
                    if (!in_array($model->handler_status, [Handler::QUEUE, Handler::DURING])) {
                        return Html::a('<i class="fa fa-2x fa-trash"></i>', Yii::$app->get('urlManagerAdmin')->createUrl(['doh/delete', 'id' => $model->handler_id]), [
                            'title' => Yii::t('doh', 'Delete'),
                            'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-doh-delete',
                            'data-pjax' => '0'
                        ]);
                    }

                    return '';
                }] : []),
            'toolbar' => array_merge(Yii::$app->getUser()->can(RbacHelper::ADMINISTRATOR) ? [
                Html::a(Yii::t('doh', 'Clear handlers'), Yii::$app->get('urlManagerAdmin')->createUrl(['doh/clear']), ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger wk-doh-clear', 'data-pjax' => '0'])
            ] : []),
            'panelHeading' => [
                'icon' => FA::icon(FA::_LIST_ALT),
                'title' => Yii::t('doh', 'Handlers'),
            ],
            'leftBottomToolbar' => Html::button('test', ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary', 'id' => 'test1'])
                . Html::button('test error', ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger', 'id' => 'test_error'])
                . Html::button('test with files', ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-info', 'id' => 'test_with_files'])
        ]) ?>

    </div>

<?php
ProgressbarAsset::register($this);
DoHAsset::register($this);
?>