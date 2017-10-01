<?php
use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;
?>

<?=
/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $gridConfig array */

GridView::widget(array_replace([
    'id' => 'RolesUserGridGrid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'description'
    ],
    'panelHeading' => array(
        'icon' => FA::icon(FA::_LIST_ALT),
        'title' => Yii::t('common/employee', 'Roles'),
    ),
], $gridConfig));
?>
