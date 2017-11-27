<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.09.2017
 * Time: 12:05
 */

/* @var $this yii\web\View */

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\CardList\CardList;
use console\helpers\RbacHelper;

Breadcrumbs::root();
$this->title = Yii::t('common/config', 'Spravochniki');
?>
<div class="sprav-index">
    <?= CardList::widget([
        'items' => [
            [
                'styleClass' => CardList::RED_STYLE,
                'icon' => 'fa fa-list-alt',
                'title' => Yii::t('common/dolzh', 'Dolzh'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/spravochniki/dolzh']),
                'roles' => RbacHelper::AUTHORIZED,
            ],
            [
                'styleClass' => CardList::RED_STYLE,
                'icon' => 'fa fa-list-alt',
                'title' => Yii::t('common/podraz', 'Podraz'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/spravochniki/podraz']),
                'roles' => RbacHelper::AUTHORIZED,
            ],
            [
                'styleClass' => CardList::GREEN_STYLE,
                'icon' => 'fa fa-list-alt',
                'title' => Yii::t('common/build', 'Build'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/spravochniki/build']),
                'roles' => RbacHelper::AUTHORIZED,
            ],
        ],
    ])
    ?>
</div>