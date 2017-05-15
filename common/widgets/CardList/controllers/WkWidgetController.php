<?php
namespace common\widgets\CardList\controllers;

use Faker\Factory;
use ReflectionClass;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\bootstrap\Html;
use yii\web\Controller;

/**
 * Site controller
 */
class WkWidgetController extends Controller
{

    public function actionScroll()
    {
        $faker = Factory::create('ru_RU');

        $oClass = new ReflectionClass(FA::class);

        $content = '';
        /** @var array $card */
        for ($i = 1; $i <= 6; $i++) {
            $contsFA = array_rand($oClass->getConstants());
            $valueConst = $oClass->getConstant($contsFA);

            $mediaContent = FA::icon($valueConst, ['class' => 'wk-style']);

            $titleContent = Html::tag('h2', Html::encode($faker->company), ['class' => 'pmd-card-title-text'])
                . Html::tag('span', Html::encode($faker->realText(rand(40, 200))), ['class' => 'pmd-card-subtitle-text']);

            $actionsContent = Html::a('ПЕРЕЙТИ', '#', ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary']);

            $ar1 = ['wk-red-style', 'wk-blue-style', 'wk-yellow-style', 'wk-green-style', 'wk-grey-style'];

            $media = Html::tag('div', $mediaContent, ['class' => 'pmd-card-media ' . $ar1[rand(0, 4)]]);

            $title = Html::tag('div', $titleContent, ['class' => 'pmd-card-title']);
            $actions = Html::tag('div', $actionsContent, ['class' => 'pmd-card-actions']);

            $content1 = Html::tag('div', $media . $title . $actions, ['class' => 'pmd-card pmd-card-default pmd-z-depth']);

            $content .= Html::tag('div', $content1, ['class' => 'col-xs-12 col-sm-6 col-md-4 wk-widget-card wk-widget-show wk-widget-scroll']);


            if ($i % 3 == 0) {
                $content .= Html::tag('div', '', ['class' => 'col-xs-12 wk-widget-card wk-widget-hide']);
            }
        }
        echo $content;

    }
}
