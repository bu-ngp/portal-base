<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 26.12.2017
 * Time: 15:52
 */

namespace common\widgets\HeaderPanel;

use common\widgets\HeaderPanel\assets\HeaderPanelAsset;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

class HeaderPanel extends Widget
{
    public $icon;
    public $title = '';

    public function run()
    {
        $icon = $this->icon ? Html::tag('div', FA::icon($this->icon), ['class' => 'wk-header-panel-icon']) : '';
        $h1 = Html::tag('h1', $this->title);

        echo Html::tag('div', $icon . $h1, ['class' => 'wk-header-panel']);

        $this->registerAssets();
    }

    protected function registerAssets()
    {
        HeaderPanelAsset::register($this->getView());
    }
}