<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.06.2017
 * Time: 12:44
 */

namespace common\widgets\Breadcrumbs;

use common\widgets\Breadcrumbs\assets\BreadcrumbsAsset;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

class Breadcrumbs extends Widget
{
    public static $show;
    public $defaultShow = true;

    public function init()
    {
        $this->registerTranslations();
        static::$show = static::$show !== null ? static::$show : $this->defaultShow;
        parent::init();
    }

    public function run()
    {
        $a=Yii::$app->id;
        $this->registerAssets();
        echo Html::tag('div', '', [
            'id' => $this->id,
            'home-crumb-url' => Yii::$app->getHomeUrl(),
            'current-crumb-id' => $this->getCurrentCrumbId(),
            'current-crumb-title' => $this->getView()->title,
        ]);
    }

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-widget-breadcrumbs'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    private function registerAssets()
    {
        $view = $this->getView();
        BreadcrumbsAsset::register($view);

        $options = [
            'homeCrumbMessage' => Yii::t('wk-widget-breadcrumbs', 'Home'),
        ];

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);

        $view->registerJs("$('#{$this->id}').wkbreadcrumbs($options);");
    }

    private function getCurrentCrumbId()
    {
        $homeId = Yii::$app->defaultRoute . '/' . Yii::$app->controller->defaultAction . '/';
        $currentId = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id . '/';

        return $homeId === $currentId || !static::$show ?: $currentId;
    }
}