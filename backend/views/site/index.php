<?php

/* @var $this yii\web\View */

use common\widgets\CardList\CardList;

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <?= CardList::widget([
        'items' => [
            /*   [
                   'styleClass' => CardList::RED_STYLE,
                   'preview' => [
                       'FAIcon' => 'list-alt',
                   ],
                   'title' => 'Роли',
                   'description' => 'Создание и редактирование ролей в системе',
                   'link' => Yii::$app->urlManager->createUrl(['roles']),
               ],*/
            [
                'styleClass' => CardList::BLUE_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => Yii::t('common/config', 'Portal configuration'),
                'link' => Yii::$app->urlManager->createUrl(['config']),
            ],
        ],
    ])
    ?>
</div>