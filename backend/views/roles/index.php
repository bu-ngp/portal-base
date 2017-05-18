<?php

use common\widgets\GridView\GridView;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Auth Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <style>
        #w0-container {
            min-height: 450px;
        }
        td>span {
            display:inline-block;
            max-width: 1000px;
            overflow: hidden; /* optional */
            white-space: nowrap;
            text-overflow: ellipsis;

            /*word-wrap: break-word;*/
        }
    </style>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\kartik\grid\SerialColumn',
                'noWrap' => true,
            ],
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'noWrap' => true,
                'rowSelectedClass' => GridView::TYPE_INFO,
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'description',
                'noWrap' => true,
            //    'width' => '500px',
                'contentOptions' => function ($model, $key, $index, $column) {
                    return ['data-toggle' => 'tooltip', 'title' => $model->description];
                },
                'format' => 'html',
                'value' =>  function ($model, $key, $index, $column) {
                    return '<span>'.$model->description .'</span>';
                },
            ],
        ],
        'hover' => true,
        'pjax' => true,
        'pjaxSettings' => [
//            'loadingCssClass' => false, //kv-grid-loading
//            'beforeGrid' => Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0'])
//                . '<a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-primary">Update</a>'
//                . '<a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-danger">Delete</a>',
//            'afterGrid' => Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0'])
//                . '<a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-primary">Update</a>'
//                . '<a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-danger">Delete</a>',
        ],
        // 'persistResize' => true,
        'resizableColumns' => false,
       // 'responsive' => false,
        // 'resizeStorageKey' => \wartron\yii2uuid\helpers\Uuid::uuid2str(Yii::$app->user->id),
        'toolbar' => [
            [
                'content' =>
                    Html::a(
                        Yii::t('app', 'Create'),
                        ['create'],
                        [
                            'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success',
                            'data-pjax' => '0'
                        ]) . ' ' .
                    '<a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-primary">Update</a>' .
                    '<a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-danger">Delete</a>',
                'options' => ['class' => 'btn-group pull-left', 'style' => 'position: absolute; bottom: 0;'],
            ],
            [
                'content' =>
                    '<a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-primary" style="text-align: right;">Filter</a>' .
                    '<a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-danger" style="text-align: right;">Export</a>' .
                    '<a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-default" style="text-align: right;">Customize</a>',
                'options' => ['class' => 'btn-group-vertical btn-group-xs pull-right'],
            ],
        ],
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="fa fa-list-alt"></i> Roles</h3>',
            'after' => <<<EOT
            <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
                <div class="btn-group pull-left">
                    <a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-success">Create</a>
                    <a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-primary">Update</a>
                    <a href="#" class="btn pmd-btn-flat pmd-ripple-effect btn-danger">Delete</a>
                </div>
            </div>
EOT
        ],
        'panelBeforeTemplate' => <<<EOT
            <div>
                <div class="btn-toolbar kv-grid-toolbar" role="toolbar" style="position: relative;">
                    {toolbar}
                </div>    
            </div>
            {before}
            <div class="clearfix"></div>
EOT
    ]);
    ?>
</div>