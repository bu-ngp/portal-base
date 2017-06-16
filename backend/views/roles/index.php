<?php

use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $filterModel \domain\models\base\filter\AuthItemFilter */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/roles', 'Roles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterDialog' => [
            'enable' => true,
            'filterModel' => $filterModel,
            'filterView' => '_filter',
        ],
        'exportGrid' => [
            'enable' => true,
            'format' => GridView::EXCEL,
            'idReportLoader' => 'wk-Report-Loader',
        ],
        'minHeight' => 450,
        'columns' => [
            'description',
            'name',
            [
                'attribute' => 'type',
                'visible' => true,
            ]
        ],
        'crudSettings' => [
            'create' => \yii\helpers\Url::to(['roles/create']),
            'update' => \yii\helpers\Url::to(['roles/update']),
            'delete' => \yii\helpers\Url::to(['roles/delete']),
        ],
        'panelHeading' => [
            'icon' => FA::icon(FA::_LIST_ALT),
            'title' => Yii::t('common/roles', 'Roles'),
        ],
        /* 'panel' => [
             'before' => '<div class="wk-filter-output"><div><span><b>Доп. фильтр: </b><span class="wk-filter-output-value">Только пользовательские роли</span>; <span class="wk-filter-output-name">Имя роли</span> = "<span class="wk-filter-output-value">hgfhg</span>"; <span class="wk-filter-output-value">Только пользовательские роли</span>; <span class="wk-filter-output-name">Имя роли</span> = "<span class="wk-filter-output-value">hgfhg</span>"; <span class="wk-filter-output-value">Только пользовательские роли</span>; <span class="wk-filter-output-name">Имя роли</span> = "<span class="wk-filter-output-value">hgfhg</span>"; <span class="wk-filter-output-value">Только пользовательские роли</span>; <span class="wk-filter-output-name">Имя роли</span> = "<span class="wk-filter-output-value">hgfhg</span>";</span></div><div><button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button></div></div>'
         ],*/
    ]);
    ?>
</div>