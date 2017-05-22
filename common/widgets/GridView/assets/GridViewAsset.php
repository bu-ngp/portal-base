<?php

namespace common\widgets\GridView\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class GridViewAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->css = [
            'css/gridview.css',
        ];

        $this->js = [
            'js/wkgridview.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'common\widgets\CardList\assets\PropellerAsset',
            'rmrevin\yii\fontawesome\AssetBundle',
        ];

        parent::init();
    }
}
