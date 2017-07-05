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

  echo  GridView::widget([
      'id'=>'test1',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
      //  'filterDialog' => GWFilterDialogConfig::set()->filterModel($filterModel),
     //   'exportGrid' => GWExportGridConfig::set()->idReportLoader('wk-Report-Loader'),
        'customizeDialog' => false,
     //   'minHeight' => 450,
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
    /*    'crudSettings' => [
            'create' => ['roles/create'],
            'update' => ['roles/update'],
            'delete' => ['roles/delete'],
        ],*/
        'panelHeading' => ['icon' => FA::icon(FA::_LIST_ALT),
            'title' => Yii::t('common/roles', 'Roles'),
        ],
    ]);
/*
$this->registerJs(<<<EOT
            if ($("#test1").length) {
                $("#test1").yiiGridView({"filterUrl": window.location.search});
                $("#test1").yiiGridView('applyFilter');
            }
EOT
);*/