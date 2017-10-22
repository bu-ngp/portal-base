<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:56
 */

use common\models\base\Person;
use common\widgets\GridView\GridView;
use console\helpers\RbacHelper;
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
            'person_code',
            'person_fullname',
            'employee.dolzh.dolzh_name',
            'employee.podraz.podraz_name',
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
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'date',
            ]
        ],
        'crudSettings' => [
            'create' => [
                'url' => 'configuration/users/create',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::USER_EDIT);
                },
            ],
            'update' => [
                'url' => 'configuration/users/update',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::USER_EDIT);
                },
            ],
            'delete' => [
                'url' => 'configuration/users/delete',
                'beforeRender' => function ($model) {
                    /** @var Person $model */
                    return !($model->person_username === 'admin') && Yii::$app->user->can(RbacHelper::USER_EDIT);
                },
            ],
        ],
        'panelHeading' => ['icon' => FA::icon(FA::_USER),
            'title' => Yii::t('common/users', 'Users'),
        ],
    ]);
    ?>
</div>