<?php

/* @var $this yii\web\View */

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\CardList\CardList;
use console\helpers\RbacHelper;

Breadcrumbs::root();
$this->title = Yii::t('common/config', 'Portal configuration');
?>
<div class="site-index">
    <?= CardList::widget([
        'items' => [
            [
                'icon' => 'fa fa-unlock-alt',
                'title' => Yii::t('common/config', 'Authorization'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/config-auth']),
                'roles' => [RbacHelper::ROLE_EDIT, RbacHelper::USER_EDIT],
            ],
            [
                'icon' => 'fa fa-sitemap',
                'title' => Yii::t('common/config', 'Common'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/config-common']),
                'roles' => [RbacHelper::ADMINISTRATOR],
            ],
        ],
    ]) ?>
</div>