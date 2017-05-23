<?php

use common\widgets\gridSelected2Input\GridSelected2InputAsset;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $modelForm domain\models\base\AuthItem */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

GridSelected2InputAsset::register($this);

$this->title = Yii::t('app', 'Create Auth Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common/authitem', 'Auth Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="auth-item-create">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'modelForm' => $modelForm,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]) ?>

    </div>

<?php
$this->registerJs(file_get_contents(__DIR__ . '/roleForm.js'));
?>