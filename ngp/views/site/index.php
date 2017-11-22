<?php

/* @var $this yii\web\View */

use common\widgets\CardList\CardList;
use domain\models\base\search\AuthItemSearch;
use ngp\services\models\search\TilesMainPageSearch;
use ngp\services\models\search\TilesSearch;
use yii\helpers\Url;

/* @var $modelSearch TilesMainPageSearch */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <?= CardList::widget([
        'url' => Url::to(['site/tiles']),
//'items' => [],
//        'items' => [
//            [
//                'styleClass' => CardList::RED_STYLE,
//                'preview' => [
//                    'FAIcon' => 'list-alt',
//                ],
//                'title' => 'Роли',
//                'description' => 'Создание и редактирование ролей в системе',
//                'link' => Yii::$app->urlManager->createUrl(['roles']),
//                'popularityID' => 'local-1',
//            ],
//            [
//                'styleClass' => CardList::RED_STYLE,
//                'preview' => [
//                    'FAIcon' => 'list-alt',
//                ],
//                'title' => 'Роли2',
//                'description' => 'Создание и редактирование ролей в системе',
//                'link' => Yii::$app->urlManager->createUrl(['roles']),
//                'popularityID' => 'local-2',
//            ],
//            [
//                'styleClass' => CardList::RED_STYLE,
//                'preview' => [
//                    'FAIcon' => 'list-alt',
//                ],
//                'title' => 'Роли3',
//                'description' => 'Создание и редактирование ролей в системе',
//                'link' => Yii::$app->urlManager->createUrl(['roles']),
//                'popularityID' => 'local-3',
//            ],
//        ],
        'search' => [
            'modelSearch' => $modelSearch,
            'searchAttributeName' => 'search_string',
        ],
        'popularity' => true,
    ])
    ?>

</div>
