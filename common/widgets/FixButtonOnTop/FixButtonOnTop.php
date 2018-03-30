<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 23.12.2017
 * Time: 17:35
 */

namespace common\widgets\FixButtonOnTop;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;

/**
 * Виджет добавляет внизу справа страницы кнопку для перехода наверх страницы.
 *
 * ```php
 * <?= FixButtonOnTop::widget() ?>
 * ```
 */
class FixButtonOnTop extends Widget
{
    /**
     * Инициализация виджета.
     */
    public function init()
    {
        $this->registerTranslations();
        parent::init();
    }

    /**
     * Регистрация сообщений i18n
     */
    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-fix-button-on-top'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    /**
     * Выполнение виджета
     */
    public function run()
    {
        echo Html::tag('div', '', ['id' => $this->id, 'class' => 'wk-fix-button-on-top', 'title' => Yii::t('wk-fix-button-on-top', 'On Top')]);
        $this->registerAssets();
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        FixButtonOnTopAsset::register($view);

        $view->registerJs("$('#{$this->id}').wkFixButtonOnTop();");
    }
}