<?php

use common\widgets\GridView\GridView;
use common\widgets\GridView\services\GWExportGridConfig;
use common\widgets\GridView\services\GWFilterDialogConfig;
use rmrevin\yii\fontawesome\FA;
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
        //'customizeDialog' => false,
        'minHeight' => 450,
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
                    //    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'DD.MM.YYYY',
                        ],
                    ],
                    'pluginEvents' => [
                        'apply.daterangepicker' => 'function(event, picker) { 
                          console.debug(picker.startDate.format("YYYY-MM-DD"));
                          console.debug(event);
                        }',
                    ],
                ],
            ],
        ],
        'crudSettings' => [
            'create' => ['roles/create'],
            'update' => ['roles/update'],
            'delete' => ['roles/delete'],
        ],
        'panelHeading' => ['icon' => FA::icon(FA::_LIST_ALT),
            'title' => Yii::t('common/roles', 'Roles'),
        ],
        'rightBottomToolbar' => Html::a('Отчет', 'roles/report',
            [
                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-default wk-report',
                'data-pjax' => '0',
                'wk-loading' => true,
            ]),
    ]);
    ?>
</div>