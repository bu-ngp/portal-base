<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.09.2017
 * Time: 12:05
 */

/* @var $this yii\web\View */

use common\widgets\CardList\CardList;

$this->title = Yii::t('common/config', 'Spravochniki');
?>
<div class="sprav-index">
    <?= CardList::widget([
        'items' => [
            [
                'styleClass' => CardList::RED_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => Yii::t('common/sprav', 'Dolzh'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/spravochniki/dolzh']),
            ],
            [
                'styleClass' => CardList::RED_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => Yii::t('common/sprav', 'Podraz'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/spravochniki/podraz']),
            ],
            [
                'styleClass' => CardList::GREEN_STYLE,
                'preview' => [
                    'FAIcon' => 'list-alt',
                ],
                'title' => Yii::t('common/sprav', 'Build'),
                'link' => Yii::$app->urlManager->createUrl(['configuration/spravochniki/build']),
            ],
        ],
    ])
    ?>
</div>