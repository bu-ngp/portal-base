<?php

/* @var $this yii\web\View */

use common\widgets\CardList\CardList;
use rmrevin\yii\fontawesome\FA;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <?=
    CardList::widget([
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
