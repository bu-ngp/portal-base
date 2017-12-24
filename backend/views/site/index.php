<?php

/* @var $this yii\web\View */

use common\widgets\CardList\CardList;
use console\helpers\RbacHelper;

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <?= CardList::widget([
        'items' => [
            [
                'icon' => 'fa fa-cogs',
                'title' => Yii::t('common/config', 'Portal configuration'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/config']),
                'roles' => [RbacHelper::ROLE_EDIT, RbacHelper::USER_EDIT],
            ],
            [
                'icon' => 'fa fa-book',
                'title' => Yii::t('common/config', 'Spravochniki'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/sprav']),
                'roles' => RbacHelper::AUTHORIZED,
            ],
        ],
    ])
    ?>
</div>