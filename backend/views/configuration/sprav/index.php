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
                'icon' => 'fa fa-user-circle-o',
                'title' => Yii::t('common/dolzh', 'Dolzhs'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/spravochniki/dolzh']),
                'roles' => RbacHelper::AUTHORIZED,
            ],
            [
                'icon' => 'fa fa-window-restore',
                'title' => Yii::t('common/podraz', 'Podrazs'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/spravochniki/podraz']),
                'roles' => RbacHelper::AUTHORIZED,
            ],
            [
                'icon' => 'fa fa-home',
                'title' => Yii::t('common/build', 'Builds'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/spravochniki/build']),
                'roles' => RbacHelper::AUTHORIZED,
            ],
        ],
    ])
    ?>
</div>