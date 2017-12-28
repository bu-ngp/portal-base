<?php

use common\widgets\GridView\GridView;
use common\widgets\HeaderPanel\HeaderPanel;
use console\helpers\RbacHelper;
use rmrevin\yii\fontawesome\FA;
use wartron\yii2uuid\helpers\Uuid;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\DolzhSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/dolzh', 'Dolzhs');
?>
<div class="dolzh-index content-container">
    <?= HeaderPanel::widget(['icon' => FA::_USER_CIRCLE_O, 'title' => Html::encode($this->title)]) ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'exportGrid' => [
            'idReportLoader' => 'wk-Report-Loader',
        ],
        'columns' => [
            'dolzh_name',
        ],
        'crudSettings' => [
            'create' => [
                'urlGrid' => 'configuration/spravochniki/dolzh/create',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::DOLZH_EDIT);
                },
            ],
            'update' => [
                'url' => 'configuration/spravochniki/dolzh/update',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::DOLZH_EDIT);
                },
            ],
            'delete' => [
                'url' => 'configuration/spravochniki/dolzh/delete',
                'beforeRender' => function () {
                    return Yii::$app->user->can(RbacHelper::DOLZH_EDIT);
                },
            ],
        ],
        'gridExcludeIdsFunc' => function (ActiveQuery $activeQuery, array $ids) {
            $activeQuery->andWhere(['not in', 'dolzh_id', $ids]);
        }
    ]);
    ?>
</div>
