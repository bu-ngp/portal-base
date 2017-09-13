<?php

/* @var $this yii\web\View */

use common\widgets\CardList\CardList;

$this->title = Yii::t('common/config', 'Authorization');
?>
<div class="site-index">
    <?= CardList::widget([
        'items' => [
            [
                'styleClass' => CardList::RED_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => Yii::t('common/roles', 'Roles'),
                'description' => 'Создание и редактирование ролей в системе',
                'link' => Yii::$app->urlManager->createUrl(['roles']),
            ],
            [
                'styleClass' => CardList::RED_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => Yii::t('common/config-ldap', 'LDAP settings'),
                'link' => Yii::$app->urlManager->createUrl(['config-ldap/update']),
            ],
        ],
    ])
    ?>
</div>