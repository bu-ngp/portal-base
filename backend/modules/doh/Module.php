<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 10:23
 */

namespace doh;

use Yii;
use yii\base\BootstrapInterface;
use yii\web\Application;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $limitHandlers = 100;

    public function bootstrap($app)
    {
        if ($app instanceof Application) {
            $app->getUrlManager()->addRules([
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/listen', 'route' => $this->id . '/default/listen'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/cancel', 'route' => $this->id . '/default/cancel'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/delete', 'route' => $this->id . '/default/delete'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/clear', 'route' => $this->id . '/default/clear'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/download', 'route' => $this->id . '/default/download'],
                //['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/download/<id:\d+>', 'route' => $this->id . '/default/download'],
            ], false);
        }
    }

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['doh'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }
}