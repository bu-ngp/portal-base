<?php

use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $gridConfig array */
?>

<?= GridView::widget(array_replace([
    'id' => 'EmployeesUserGrid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'dolzh_name',
        'podraz_name',
        [
            'attribute' => 'employee_history_begin',
            'format' => 'datetime',
            'filterWidgetOptions' => [
                //    'convertFormat' => true,
                'pluginEvents' => [
                    'apply.daterangepicker' => 'function(event, picker) {
                              console.debug(picker.startDate.format("YYYY-MM-DD"));
                              console.debug(event);
                            }',
                ],
            ],
        ],
        [
            'attribute' => 'employee_history_end',
            'format' => 'datetime',
            'filterWidgetOptions' => [
                //    'convertFormat' => true,
                'pluginEvents' => [
                    'apply.daterangepicker' => 'function(event, picker) {
                              console.debug(picker.startDate.format("YYYY-MM-DD"));
                              console.debug(event);
                            }',
                ],
            ],
        ],
        'employee_type',
    ],
    'panelHeading' => array(
        'icon' => FA::icon(FA::_LIST_ALT),
        'title' => Yii::t('common/employee', 'Employees'),
    ),
], $gridConfig)) ?>