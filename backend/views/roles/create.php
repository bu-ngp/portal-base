<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $modelForm domain\models\base\AuthItem */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Create Auth Item');

?>
    <div class="auth-item-create">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'modelForm' => $modelForm,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'crudSettings' => $crudSettings,
        ]) ?>

    </div>

<?php
$this->registerJs(file_get_contents(__DIR__ . '/roleForm.js'));
?>