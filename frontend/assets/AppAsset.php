<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/assets';
    public $css = [
    ];
    public $js = [
    ];
    public $depends = [
        'common\assets\AppCommonAsset',
    ];
}
