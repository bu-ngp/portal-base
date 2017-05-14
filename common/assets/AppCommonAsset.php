<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppCommonAsset extends AssetBundle
{
    public $sourcePath = '@app/../common/assets';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/proc.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\assets\PropellerAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];
}
