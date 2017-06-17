<?php

use common\widgets\GridView\GridView;
use common\widgets\GridView\services\GWExportGridConfig;
use common\widgets\GridView\services\GWFilterDialogConfig;
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
        'filterDialog' => GWFilterDialogConfig::set()->filterModel($filterModel),
        'exportGrid' => GWExportGridConfig::set()->idReportLoader('wk-Report-Loader')->format(GridView::PDF),
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
    ]);
    ?>
</div>