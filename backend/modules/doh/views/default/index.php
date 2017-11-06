<?php

use doh\assets\DoHAsset;
use doh\assets\ProgressbarAsset;
use doh\services\models\Handler;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;
use yii\helpers\Url;

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
                    'format' => 'raw',
                    'value' => function ($model, $key) {
                        return '<div class="wk-progress" key="' . $key . '" percent="' . (round($model->handler_percent / 100, 2)) . '"></div>';
                    },
                ],
                [
                    'attribute' => 'handler_short_report',
                    'filter' => false,
                ],
                [
                    'attribute' => 'dohFilesList',
                    'label' => Yii::t('doh', 'Handler Files'),
                    'format' => 'raw',
                    'filter' => false,
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
            'customActionButtons' => [
                'cancel' => function ($url, $model) {
                    if (in_array($model->handler_status, [Handler::QUEUE, Handler::DURING])) {
                        return Html::a('<i class="fa fa-2x fa-close"></i>', Yii::$app->get('urlManagerAdmin')->createUrl(['doh/cancel', 'id' => $model->handler_id]), [
                            'title' => Yii::t('doh', 'Cancel'),
                            'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-doh-cancel',
                            'data-pjax' => '0'
                        ]);
                    }

                    return '';
                },
                'delete' => function ($url, $model) {
                    if (!in_array($model->handler_status, [Handler::QUEUE, Handler::DURING])) {
                        return Html::a('<i class="fa fa-2x fa-trash"></i>', Yii::$app->get('urlManagerAdmin')->createUrl(['doh/delete', 'id' => $model->handler_id]), [
                            'title' => Yii::t('doh', 'Delete'),
                            'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-doh-delete',
                            'data-pjax' => '0'
                        ]);
                    }

                    return '';
                },
            ],
            'toolbar' => [
                \yii\bootstrap\Html::a(Yii::t('doh', 'Clear handlers'), Yii::$app->get('urlManagerAdmin')->createUrl(['doh/clear']), ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger wk-doh-clear', 'data-pjax' => '0'])
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_LIST_ALT),
                'title' => Yii::t('doh', 'Handlers'),
            ],
            'leftBottomToolbar' => \yii\bootstrap\Html::button('test', ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary', 'id' => 'test1'])
                . \yii\bootstrap\Html::button('test error', ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger', 'id' => 'test_error'])
                . \yii\bootstrap\Html::button('test with files', ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-info', 'id' => 'test_with_files'])
        ]) ?>

    </div>

<?php
ProgressbarAsset::register($this);
DoHAsset::register($this);
?>