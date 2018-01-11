<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 07.01.2018
 * Time: 10:46
 */

/* @var $searchModelChooseBuild \domain\models\base\search\BuildSearch */

/* @var $dataProviderChooseBuild yii\data\ActiveDataProvider */

use common\widgets\GridView\GridView;

?>

<?= \yii\bootstrap\Html::input('hidden', 'chooseGrid') ?>

<?= GridView::widget([
    'id' => 'searchModelChooseBuildGrid',
    'dataProvider' => $dataProviderChooseBuild,
    'filterModel' => $searchModelChooseBuild,
    'customizeDialog' => false,
    'columns' => [
        'build_name',
    ],
    'crudSettings' => [
        'create' => [
            'urlGrid' => 'configuration/spravochniki/build/index',
            'inputName' => 'chooseGrid',
        ],
        'delete' => [
            'inputName' => 'chooseGrid',
        ],
    ],
]) ?>