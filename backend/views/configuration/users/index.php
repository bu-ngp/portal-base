<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:56
 */

use domain\models\base\Person;
use common\widgets\GridView\GridView;
use console\helpers\RbacHelper;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\UsersSearch */
/* @var $filterModel domain\models\base\filter\UsersFilter */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/person', 'Users');
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
        'exportGrid' => [
            'idReportLoader' => 'wk-Report-Loader',
        ],
        //'customizeDialog' => false,
        //  'minHeight' => 450,
        'columns' => [
            'person_code',
            [
                'attribute' => 'person_username',
                'visible' => false,
            ],
            'person_fullname',
            [
                'attribute' => 'person_email',
                'visible' => false,
            ],
            [
                'attribute' => 'employee.dolzh.dolzh_name',
                'label' => Yii::t('domain/employee', 'Dolzh ID'),
            ],
            [
                'attribute' => 'employee.podraz.podraz_name',
                'label' => Yii::t('domain/employee', 'Podraz ID'),
            ],
            [
                'attribute' => 'person_hired',
                'format' => 'date',
                'visible' => false,
            ],
            [
                'attribute' => 'person_fired',
                'format' => 'date',
            ],
            [
                'attribute' => 'profile.profile_dr',
                'format' => 'date',
                'visible' => false,
            ],
           // [
                /*'attribute' =*/ 'profile.profile_pol',
            //],
            [
                'attribute' => 'profile.profile_inn',
                'visible' => false,
            ],
            [
                'attribute' => 'profile.profile_snils',
                'value' => function ($model, $key, $index, $column) {
                    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1-$2-$3 $4', \yii\helpers\ArrayHelper::getValue($model, $column->attribute));
                },
                'visible' => false,
            ],
            [
                'attribute' => 'profile.profile_address',
                'visible' => false,
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'visible' => false,
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'visible' => false,
            ],
            [
                'attribute' => 'created_by',
                'visible' => false,
            ],
            [
                'attribute' => 'updated_by',
                'visible' => false,
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
            'title' => Yii::t('common/person', 'Users'),
        ],
    ]);
    ?>
</div>