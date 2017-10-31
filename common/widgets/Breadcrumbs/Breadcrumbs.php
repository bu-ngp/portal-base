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
use yii\helpers\Url;
use yii\web\View;

class Breadcrumbs extends Widget
{
    public static $show;
    public $defaultShow = true;
    public static $cookieId = 'wk_breadcrumb';
    public static $root = false;

    public function init()
    {
        $this->id = 'wkbc_' . Yii::$app->id;
        $this->registerTranslations();
        static::$show = static::$show !== null ? static::$show : $this->defaultShow;
        parent::init();
    }

    public function run()
    {
        echo Html::tag('div', '', [
            'id' => $this->id,
            'home-crumb-url' => Yii::$app->getHomeUrl(),
            'current-crumb-id' => $this->getCurrentCrumbId(),
            'current-crumb-title' => $this->getView()->title,
            'remove-last-crumb' => $this->getRemoveLastCrumb() ? '1' : '0',
            'root' => self::$root ? '1' : '0',
            'cookie-id' => self::$cookieId,
            'class' => 'wkbc-breadcrumb',
        ]);
        $this->registerAssets();
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
            'CurrentPageMessage' => Yii::t('wk-widget-breadcrumbs', 'Current Page'),
        ];

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);

        $view->registerJs("$('#{$this->id}').wkbreadcrumbs($options);");
        array_unshift($view->js[View::POS_READY], array_pop($view->js[View::POS_READY]));
    }

    private function getCurrentCrumbId()
    {
        $homeId = Yii::$app->defaultRoute . '/' . Yii::$app->controller->defaultAction . '/';
        $currentId = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id . '/';

        return $homeId === $currentId || !static::$show ?: $currentId;
    }

    public static function removeLastCrumb()
    {
        Yii::$app->session->set('_wkbc_remove_last_crumb', true);
    }

    protected function getRemoveLastCrumb()
    {
        if (Yii::$app->session->get('_wkbc_remove_last_crumb') === true) {
            Yii::$app->session->remove('_wkbc_remove_last_crumb');

            return true;
        }

        return false;
    }

    public static function previousUrl()
    {
        if ($wkbcObject = json_decode($_COOKIE[self::$cookieId])) {
            return $wkbcObject->previousUrl;
        } else {
            return Url::previous();
        }
    }

    public static function root() {
        self::$root = true;
    }
}