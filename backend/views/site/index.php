<?php

/* @var $this yii\web\View */

use common\widgets\CardList\CardList;
use rmrevin\yii\fontawesome\FA;

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="row pmd-z-depth" style="margin-bottom: 20px">
        <div class="col-xs-1" style="top: 15px; text-align: right;">
            <i class="fa fa-map-marker fa-4x" style="color: rgba(0, 0, 0, 0.4)"></i>
        </div>
        <div class="col-xs-11">
            <div class="form-group pmd-textfield pmd-textfield-floating-label form-group-lg">
                <label for="search_cards" class="control-label">Поиск</label>
                <input type="text" id="search_cards" class="form-control input-group-lg">
            </div>
        </div>
    </div>
    <!--    <div class="input-group-addon"><i class="fa fa-search fa-2x"></i></div>-->
    <?=
    CardList::widget([
        'url' => '/wk-portal_dev/site/test',
        'items' => [
            [
                'preview' => 'http://propeller.in/assets/images/profile-pic.png',
                'title' => 'Департамент здравоохранения Ханты-Мансийского автономного округа - Югры',
                'description' => 'Официальный сайт Департамента здравоохранения Ханты-Мансийского автономного округа - Югры',
                'link' => Yii::$app->urlManagerAdmin->createUrl(['/']),
            ],
            [
                'styleClass' => CardList::RED_STYLE,
                'preview' => [
                    'FAIcon' => 'cog',
                ],
                'title' => 'Департамент здравоохранения Ханты-Мансийского автономного округа - Югры',
                'description' => 'Официальный сайт Департамента здравоохранения Ханты-Мансийского автономного округа - Югры',
                'link' => Yii::$app->urlManagerAdmin->createUrl(['/']),
            ],
            [
                'styleClass' => CardList::BLUE_STYLE,
                'preview' => [
                    'FAIcon' => 'superpowers',
                ],
                'title' => 'Тест1',
                'description' => 'Описание1',
                'link' => Yii::$app->urlManagerAdmin->createUrl(['/']),
            ],
            [
                'styleClass' => CardList::GREY_STYLE,
                'preview' => [
                    'FAIcon' => 'user-o',
                ],
                'title' => 'Тест2',
                'description' => 'Описание2',
                'link' => Yii::$app->urlManagerAdmin->createUrl(['/']),
            ],
            [
                'styleClass' => CardList::GREY_STYLE,
                'preview' => [
                    'FAIcon' => 'id-card',
                ],
                'title' => 'Тест3',
                'description' => 'Описание3',
                'link' => Yii::$app->urlManagerAdmin->createUrl(['/']),
            ],
        ],
    ])
    ?>
</div>
