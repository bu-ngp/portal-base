<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 23.12.2017
 * Time: 18:16
 */

namespace common\widgets\FixButtonBackward;

use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

/**
 * Виджет добавляет внизу слева страницы кнопку для перехода на предыдущую страницу.
 *
 * ```php
 * <?= FixButtonBackward::widget() ?>
 * ```
 */
class FixButtonBackward extends Widget
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
        $i18n->translations['wk-fix-button-backward'] = [
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
        echo Html::tag('div', '', ['id' => $this->id, 'class' => 'wk-fix-button-backward', 'title' => Yii::t('wk-fix-button-backward', 'Backward')]);
        $this->registerAssets();
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        FixButtonBackwardAsset::register($view);

        $view->registerJs("$('#{$this->id}').wkFixButtonBackward();");
    }
}