<?php

/* @var $this yii\web\View */

use common\widgets\CardList\CardList;
use console\helpers\RbacHelper;

$this->title = Yii::t('common/config', 'Authorization');
?>
<div class="site-index">
    <?= CardList::widget([
        'items' => [
            [
                'styleClass' => CardList::RED_STYLE,
                'icon' => 'fa fa-list-alt',
                'title' => Yii::t('common/roles', 'Roles'),
                'description' => 'Создание и редактирование ролей в системе',
                'link' => Yii::$app->urlManager->createUrl(['configuration/roles']),
                'roles' => RbacHelper::ROLE_EDIT,
            ],
            [
                'styleClass' => CardList::RED_STYLE,
                'icon' => 'fa fa-list-alt',
                'title' => Yii::t('common/config-ldap', 'LDAP settings'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/config-ldap/update']),
                'roles' => RbacHelper::ROLE_EDIT,
            ],
            [
                'styleClass' => CardList::GREEN_STYLE,
                'icon' => 'fa fa-list-alt',
                'title' => Yii::t('common/config-ldap', 'Users of system'),
                'description' => 'Пользователи системы',
                'link' => Yii::$app->urlManager->createUrl(['configuration/users']),
                'roles' => RbacHelper::USER_EDIT,
            ],
        ],
    ]) ?>
</div>