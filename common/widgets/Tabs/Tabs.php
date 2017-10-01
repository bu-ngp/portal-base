<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 12.09.2017
 * Time: 8:41
 */

namespace common\widgets\Tabs;

use common\widgets\Tabs\assets\TabsAsset;
use yii\base\InvalidConfigException;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

class Tabs extends \yii\bootstrap\Tabs
{
    public $options = ['role' => 'tablist'];
    public $headerOptions = ['role' => 'presentation'];
    public $linkOptions = ['role' => 'tab'];
    public $itemOptions = ['role' => 'tabpanel'];

    public function run()
    {
        $this->registerAssets();
        return parent::run();
    }

    protected function renderItems()
    {
        $headers = [];
        $panes = [];

        if (!$this->hasActiveTab() && !empty($this->items)) {
            $this->items[0]['active'] = true;
        }

        foreach ($this->items as $n => $item) {
            if (!ArrayHelper::remove($item, 'visible', true)) {
                continue;
            }
            if (!array_key_exists('label', $item)) {
                throw new InvalidConfigException("The 'label' option is required.");
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $headerOptions = array_merge($this->headerOptions, ArrayHelper::getValue($item, 'headerOptions', []));
            $linkOptions = array_merge($this->linkOptions, ArrayHelper::getValue($item, 'linkOptions', []));

            if (isset($item['items'])) {
                $label .= ' <b class="caret"></b>';
                Html::addCssClass($headerOptions, ['widget' => 'dropdown']);

                if ($this->renderDropdown($n, $item['items'], $panes)) {
                    Html::addCssClass($headerOptions, 'active');
                }

                Html::addCssClass($linkOptions, ['widget' => 'dropdown-toggle']);
                if (!isset($linkOptions['data-toggle'])) {
                    $linkOptions['data-toggle'] = 'dropdown';
                }
                $header = Html::a($label, "#", $linkOptions) . "\n"
                    . Dropdown::widget(['items' => $item['items'], 'clientOptions' => false, 'view' => $this->getView()]);
            } else {
                $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
                $options['id'] = ArrayHelper::getValue($options, 'id', $this->options['id'] . '-tab' . $n);
                $linkOptions['aria-controls'] = ArrayHelper::getValue($options, 'id', $this->options['id']);

                Html::addCssClass($options, ['widget' => 'tab-pane fade' . ($n ? '' : ' in')]);
                if (ArrayHelper::remove($item, 'active')) {
                    Html::addCssClass($options, 'active');
                    Html::addCssClass($headerOptions, 'active');
                }

                if (isset($item['url'])) {
                    $header = Html::a($label, $item['url'], $linkOptions);
                } else {
                    if (!isset($linkOptions['data-toggle'])) {
                        $linkOptions['data-toggle'] = 'tab';
                    }
                    $header = Html::a($label, '#' . $options['id'], $linkOptions);
                }

                if ($this->renderTabContent) {
                    $tag = ArrayHelper::remove($options, 'tag', 'div');
                    $panes[] = Html::tag($tag, isset($item['content']) ? $item['content'] : '', $options);
                }
            }

            $headers[] = Html::tag('li', $header, $headerOptions);
        }

        return $this->container(Html::tag('ul', implode("\n", $headers), $this->options), $this->renderTabContent ? "\n" . Html::tag('div', implode("\n", $panes), ['class' => 'tab-content']) : '');

//        return Html::tag('ul', implode("\n", $headers), $this->options)
//        . ($this->renderTabContent ? "\n" . Html::tag('div', implode("\n", $panes), ['class' => 'tab-content']) : '');
    }

    protected function container($ul, $tabContent)
    {
        return Html::tag('div',
            Html::tag('div', Html::tag('div', '', ['class' => 'pmd-tab-active-bar']) . $ul, ['class' => 'pmd-tabs']) .
            Html::tag('div', $tabContent, ['class' => 'pmd-card-body'])
            , ['class' => 'pmd-card pmd-z-depth']);
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        TabsAsset::register(self::getView());

        $view->registerJs("$(document).find('.pmd-tabs').pmdTab()");
    }

}