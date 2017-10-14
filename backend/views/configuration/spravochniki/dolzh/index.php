<?php

use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;
use wartron\yii2uuid\helpers\Uuid;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\DolzhSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/dolzh', 'Dolzhs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dolzh-index">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'create' => 'configuration/spravochniki/dolzh/create',
            'update' => 'configuration/spravochniki/dolzh/update',
            'delete' => 'configuration/spravochniki/dolzh/delete',
        ],
        'panelHeading' => [
            'icon' => FA::icon(FA::_USER),
            'title' => Yii::t('common/dolzh', 'Dolzhs'),
        ],
        'gridExcludeIdsFunc' => function (ActiveQuery $activeQuery, array $ids) {
            $activeQuery->andWhere(['not in', 'dolzh_id', $ids]);
//                    ->andWhere(['not exists', (new Query())
//                        ->select('{{%auth_item_child}}.child')
//                        ->from('{{%auth_item_child}}')
//                        ->andWhere(['in', '{{%auth_item_child}}.parent', $ids])
//                        ->andWhere('{{%auth_item_child}}.child = {{%auth_item}}.name')
//                    ]);
        }
    ]);
    ?>
</div>
