<?php

use common\widgets\GridView\GridView;
use common\widgets\HeaderPanel\HeaderPanel;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $gridExcludeIdsFunc Closure */

$this->title = Yii::t('common/roles', 'Roles');
?>
<div class="auth-item-index content-container">
    <?= HeaderPanel::widget(['icon' => FA::_LIST_ALT, 'title' => Html::encode($this->title)]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'description',
        ],
        'gridExcludeIdsFunc' => $gridExcludeIdsFunc,
    ]);
    ?>
</div>