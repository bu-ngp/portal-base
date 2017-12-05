<?php

use console\helpers\RbacHelper;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\BuildSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/build', 'Builds');
?>
<div class="build-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'exportGrid' => [
            'idReportLoader' => 'wk-Report-Loader',
        ],
        'columns' => [
            'build_name',
        ],
        'crudSettings' => [
            'create' => [
                'url' => 'configuration/spravochniki/build/create',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::BUILD_EDIT);
                },
            ],
            'update' => [
                'url' => 'configuration/spravochniki/build/update',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::BUILD_EDIT);
                },
            ],
            'delete' => [
                'url' => 'configuration/spravochniki/build/delete',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::BUILD_EDIT);
                },
            ],
        ],
        'panelHeading' => [
            'icon' => FA::icon(FA::_HOME),
            'title' => Yii::t('common/build', 'Builds'),
        ],
        'gridExcludeIdsFunc' => function (ActiveQuery $activeQuery, array $ids) {
            $activeQuery->andWhere(['not in', 'build_id', $ids]);
        }
    ]); ?>

</div>
