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
            'format' => 'date',
        ],
        [
            'attribute' => 'employee_history_end',
            'format' => 'date',
        ],
        'employee_type',
    ],
    'panelHeading' => array(
        'icon' => FA::icon(FA::_USERS),
        'title' => Yii::t('common/employee', 'Employees'),
    ),
], $gridConfig)) ?>