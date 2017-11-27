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
                'styleClass' => CardList::RED_STYLE,
                'icon' => 'fa fa-list-alt',
                'title' => Yii::t('common/config', 'Authorization'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/config-auth']),
                'roles' => [RbacHelper::ROLE_EDIT, RbacHelper::USER_EDIT],
            ],
        ],
    ]) ?>
</div>