<?php

/* @var $this yii\web\View */
use common\widgets\CardList\CardList;
use domain\models\base\search\AuthItemSearch;
use yii\helpers\Url;

/* @var $modelSearch AuthItemSearch */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <?= CardList::widget([
        'url' => Url::to(['site/test']),
        'search' => [
            'modelSearch' => $modelSearch,
            'searchAttributeName' => 'description',
        ],
        'popularity' => true,
    ])
    ?>

</div>
