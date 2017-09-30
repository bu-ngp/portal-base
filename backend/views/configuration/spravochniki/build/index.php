<?php

use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;

/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\BuildSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common/build', 'Builds');
$this->params['breadcrumbs'][] = $this->title;
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
                'create' => 'configuration/spravochniki/build/create',
                'update' => 'configuration/spravochniki/build/update',
                'delete' => 'configuration/spravochniki/build/delete',
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_BARS),
                'title' => Yii::t('common/build', 'Builds'),
            ],
    ]); ?>

</div>
