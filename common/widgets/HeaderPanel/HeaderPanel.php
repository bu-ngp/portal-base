<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 26.12.2017
 * Time: 15:52
 */

namespace common\widgets\HeaderPanel;

use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

/**
 * Виджет панель-заголовок.
 * ---
 * ```php
 * <?= HeaderPanel::widget([
 *         'icon' => FA::_LIST_ALT, // 'list-alt'
 *         'title' => Html::encode($this->title),
 * ]) ?>
 * ```
 */
class HeaderPanel extends Widget
{
    /**
     * @var string Имя иконки FontAwesome в начале панели заголовка.
     *
     * ```php
     * <?= HeaderPanel::widget(['icon' => FA::_LIST_ALT, 'title' => Html::encode($this->title)]) ?>
     * ```
     */
    public $icon;

    /**
     * @var string Имя панели заголовка.
     *
     * ```php
     * <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>
     * ```
     */
    public $title = '';

    /**
     * [\yii\base\Widget::run()](https://www.yiiframework.com/doc/api/2.0/yii-base-widget#run()-detail)
     */
    public function run()
    {
        $icon = $this->icon ? Html::tag('div', FA::icon($this->icon), ['class' => 'wk-header-panel-icon']) : '';
        $h1 = Html::tag('h1', $this->title);

        echo Html::tag('div', $icon . $h1, ['class' => 'wk-header-panel']);

        $this->registerAssets();
    }

    /**
     * Зарегистрировать AssetBundle [[HeaderPanelAsset::init]]
     */
    protected function registerAssets()
    {
        HeaderPanelAsset::register($this->getView());
    }
}