<?php

namespace common\assets;

use yii\web\AssetBundle;

class UserAsset extends AssetBundle
{
    public $sourcePath = '@app/../common/assets';

    public $js = [
        'js/user.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
