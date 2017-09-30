<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:56
 */

use common\models\base\Person;
use common\widgets\GridView\GridView;
use domain\models\base\AuthItem;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\UsersSearch */
/* @var $filterModel domain\models\base\filter\UsersFilter */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/users', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        /* 'filterDialog' => [
             'filterModel' => $filterModel,
             //    'filterView' => '_filter_old',
         ],*/
        /*  'exportGrid' => [
              'idReportLoader' => 'wk-Report-Loader',
          ],*/
        //'customizeDialog' => false,
        //  'minHeight' => 450,
        'columns' => [
            'person_fullname',

            'employee.dolzh.dolzh_name',
            'employee.podraz.podraz_name',
            'employee.build.build_name',
            [
                'attribute' => 'person_fired',
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
        ],
        'crudSettings' => [
            'create' => 'configuration/users/create',
            'update' => 'configuration/users/update',
            'delete' => [
                'url' => 'configuration/users/delete',
                'beforeRender' => function ($model) {
                    /** @var Person $model */
                    return !($model->person_username === 'admin');
                },
            ],
        ],
        'panelHeading' => ['icon' => FA::icon(FA::_USER),
            'title' => Yii::t('common/users', 'Users'),
        ],
    ]);
    ?>
</div>