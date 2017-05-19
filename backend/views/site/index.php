<?php

/* @var $this yii\web\View */
/* @var $modelSearch AuthItemSearch */

use common\widgets\CardList\CardList;
use domain\models\base\search\AuthItemSearch;
use rmrevin\yii\fontawesome\FA;

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <!--    <div class="row pmd-z-depth wk-widget-search-panel">-->
    <!--        <div class="col-xs-1 wk-widget-search-panel-icon">-->
    <!--            <i class="fa fa-map-marker fa-4x"></i>-->
    <!--        </div>-->
    <!--        <div class="col-xs-11 wk-widget-search-panel-field">-->
    <!--            <div class="form-group pmd-textfield pmd-textfield-floating-label form-group-lg">-->
    <!--                <label for="search_cards" class="control-label">Поиск</label>-->
    <!--                <input type="text" id="search_cards" class="form-control input-group-lg">-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <?= CardList::widget([
        'url' => '/wk-portal_dev/site/test',
        'search' => [
            'modelSearch' => $modelSearch,
            'searchAttributeName' => 'description',
        ],
        'items' => [
            [
                'styleClass' => CardList::RED_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => 'Роли',
                'description' => 'Создание и редактирование ролей в системе',
                'link' => Yii::$app->urlManager->createUrl(['roles']),
            ], [
                'styleClass' => CardList::BLUE_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => 'Пользователи',
                'description' => 'продвинутые люди',
                'link' => Yii::$app->urlManager->createUrl(['roles']),
            ], [
                'styleClass' => CardList::GREY_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => 'Админы',
                'description' => 'серьезные люди',
                'link' => Yii::$app->urlManager->createUrl(['roles']),
            ]
        ],
    ])
    ?>
</div>
