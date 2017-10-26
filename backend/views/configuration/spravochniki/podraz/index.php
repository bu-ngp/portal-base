<?php

use console\helpers\RbacHelper;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\PodrazSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/podraz', 'Podrazs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="podraz-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'exportGrid' => [
            'idReportLoader' => 'wk-Report-Loader',
        ],
        'columns' => [
            'podraz_name',
        ],
        'crudSettings' => [
            'create' => [
                'url' => 'configuration/spravochniki/podraz/create',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::PODRAZ_EDIT);
                },
            ],
            'update' => [
                'url' => 'configuration/spravochniki/podraz/update',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::PODRAZ_EDIT);
                },
            ],
            'delete' => [
                'url' => 'configuration/spravochniki/podraz/delete',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::PODRAZ_EDIT);
                },
            ],
        ],
        'panelHeading' => [
            'icon' => FA::icon(FA::_WINDOW_RESTORE),
            'title' => Yii::t('common/podraz', 'Podrazs'),
        ],
        'gridExcludeIdsFunc' => function (ActiveQuery $activeQuery, array $ids) {
            $activeQuery->andWhere(['not in', 'podraz_id', $ids]);
        }
    ]) ?>

</div>
