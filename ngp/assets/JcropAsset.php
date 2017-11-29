<?php

namespace ngp\assets;

use yii\web\AssetBundle;

class JcropAsset extends AssetBundle
{
    public $sourcePath = '@ngpVendor/bower-asset/jcrop/';

    public $js = [
        'js/jquery.Jcrop.min.js'
    ];

    public $css = [
        'css/jquery.Jcrop.min.css'
    ];
}