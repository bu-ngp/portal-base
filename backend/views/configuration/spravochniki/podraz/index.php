<?php

use common\widgets\HeaderPanel\HeaderPanel;
use console\helpers\RbacHelper;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\PodrazSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/podraz', 'Podrazs');
?>
<div class="podraz-index content-container">
    <?= HeaderPanel::widget(['icon' => FA::_WINDOW_RESTORE, 'title' => Html::encode($this->title)]) ?>

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
        'gridExcludeIdsFunc' => function (ActiveQuery $activeQuery, array $ids) {
            $activeQuery->andWhere(['not in', 'podraz_id', $ids]);
        }
    ]) ?>

</div>
