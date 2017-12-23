<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 23.12.2017
 * Time: 17:35
 */

namespace common\widgets\FixButtonOnTop;


use common\widgets\FixButtonOnTop\assets\FixButtonOnTopAsset;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;

class FixButtonOnTop extends Widget
{
    public function init()
    {
        $this->registerTranslations();
        parent::init();
    }

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-fix-button-on-top'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    public function run()
    {
        echo Html::tag('div', '', ['id' => $this->id, 'class' => 'wk-fix-button-on-top','title' => Yii::t('wk-fix-button-on-top', 'On Top')]);
        $this->registerAssets();
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        FixButtonOnTopAsset::register($view);

        $view->registerJs("$('#{$this->id}').wkFixButtonOnTop();");
    }
}