<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 07.01.2018
 * Time: 10:46
 */

/* @var $searchModelAuthitem \domain\models\base\search\AuthItemSearch */
/* @var $dataProviderAuthitem yii\data\ActiveDataProvider */

/* @var $filterModelAuthitem \domain\models\base\filter\AuthItemTestFilter */

use common\widgets\GridView\GridView;

?>

<?= GridView::widget([
    'dataProvider' => $dataProviderAuthitem,
    'filterModel' => $searchModelAuthitem,
    'filterDialog' => [
        'filterModel' => $filterModelAuthitem,
        'filterView' => '_test_grid_filter',
    ],
    'exportGrid' => [
        'idReportLoader' => 'wk-Report-Loader',
    ],
    'minHeight' => 450,
    'columns' => [
        'description',
        'type',
        [
            'attribute' => 'view',
        ],
        [
            'attribute' => 'created_at',
            'format' => 'date',
            'visible' => true,
        ],
        [
            'attribute' => 'updated_at',
            'format' => 'date',
            'visible' => false,
        ],
    ],
    'crudSettings' => [
        'delete' => [
            'url' => 'configuration/roles/delete',
            'beforeRender' => function () {
                return false;
            },
        ],
    ],
//        'rightBottomToolbar' => Html::a('Отчет', 'roles/report',
//            [
//                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-default wk-report',
//                'data-pjax' => '0',
//                'wk-loading' => true,
//            ]),
]) ?>
