<?php

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
                'create' => 'configuration/spravochniki/podraz/create',
                'update' => 'configuration/spravochniki/podraz/update',
                'delete' => 'configuration/spravochniki/podraz/delete',
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_BARS),
                'title' => Yii::t('common/podraz', 'Podrazs'),
            ],
    ]); ?>

</div>
