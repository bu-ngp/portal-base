<?php

namespace common\widgets\CardList\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class CardListAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->css = [
            'css/cardlist.css',
        ];

        $this->js = [
            'js/wkcardlist.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'common\widgets\CardList\assets\PropellerAsset',
            'common\widgets\CardList\assets\MasonryAssets',
            'rmrevin\yii\fontawesome\AssetBundle',
        ];

        parent::init();
    }
}
