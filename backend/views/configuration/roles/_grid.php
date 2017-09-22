<?php
use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;

?>

<?=
/* @var $this yii\web\View */
/* @var $modelForm domain\models\base\AuthItem */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $gridConfig array */

GridView::widget(array_replace([
    'id' => $modelForm->formName() . 'Grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'description'
    ],
    'panelHeading' => array(
        'icon' => FA::icon(FA::_LIST_ALT),
        'title' => Yii::t('common/roles', 'Roles'),
    ),
], $gridConfig));
?>