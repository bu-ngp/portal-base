<?php

use common\widgets\GridView\GridView;
use common\widgets\GridView\services\GWFilterDialogConfig;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $filterModel \domain\models\base\filter\AuthItemFilter */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/roles', 'Roles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterDialog' => [
            'filterModel' => $filterModel,
        ],
        'columns' => [
            'description',
            'name',
            [
                'attribute' => 'type',
                'visible' => true,
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
        ],
        'gridExcludeIdsFunc' => function (\yii\db\ActiveQuery $activeQuery, array $ids) {
            $activeQuery
                ->andWhere(['not in', 'name', $ids])
                ->andWhere(['not exists', (new \yii\db\Query())
                    ->select('{{%auth_item_child}}.child')
                    ->from('{{%auth_item_child}}')
                    ->andWhere(['in', '{{%auth_item_child}}.parent', $ids])
                    ->andWhere('{{%auth_item_child}}.child = {{%auth_item}}.name')
                ]);
        },
        'panelHeading' => ['icon' => FA::icon(FA::_LIST_ALT),
            'title' => Yii::t('common/roles', 'Roles'),
        ],
    ]);
    ?>
</div>