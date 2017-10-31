<?php

/* @var $this yii\web\View */

use common\widgets\CardList\CardList;
use console\helpers\RbacHelper;

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
                'link' => Yii::$app->urlManager->createUrl(['configuration/config']),
                'roles' => [RbacHelper::ROLE_EDIT, RbacHelper::USER_EDIT],
            ],
//            [
//                'styleClass' => CardList::YELLOW_STYLE,
//                'preview' => [
//                    'FAIcon' => 'list-alt',
//                ],
//                'title' => Yii::t('common/config', 'Tiles on main page'),
//                'description' => 'Добавление/Редактирование/Удаление плиток',
//                'link' => Yii::$app->urlManager->createUrl(['configuration/tiles']),
//            ],
            [
                'styleClass' => CardList::GREEN_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => Yii::t('common/config', 'Spravochniki'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/sprav']),
                'roles' => RbacHelper::AUTHORIZED,
            ],
        ],
    ])
    ?>
</div>