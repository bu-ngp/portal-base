<?php

use common\widgets\GridView\GridView;
use console\helpers\RbacHelper;
use domain\models\base\AuthItem;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $filterModel \domain\models\base\filter\AuthItemFilter */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
        'exportGrid' => [
            'idReportLoader' => 'wk-Report-Loader',
        ],
        'minHeight' => 450,
        'columns' => [
            'description',
            [
                'attribute' => 'name',
                'visible' => false,
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'date',
                'visible' => false,
            ],
        ],
        'crudSettings' => [
            'create' => 'configuration/roles/create',
            'update' => 'configuration/roles/update',
            'delete' => [
                'url' => 'configuration/roles/delete',
                'beforeRender' => function ($model) {
                    /** @var AuthItem $model */
                    return !($model->view || $model->name === RbacHelper::ADMINISTRATOR);
                },
            ],
        ],
        'panelHeading' => ['icon' => FA::icon(FA::_LIST_ALT),
            'title' => Yii::t('common/roles', 'Roles'),
        ],
//        'rightBottomToolbar' => Html::a('Отчет', 'roles/report',
//            [
//                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-default wk-report',
//                'data-pjax' => '0',
//                'wk-loading' => true,
//            ]),
    ]) ?>
</div>