<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.11.2017
 * Time: 11:10
 */

namespace ngp\assets;


use yii\web\AssetBundle;

class OfomsAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $css = [
        'css/ofoms.css',
    ];

    public $js = [
        'js/ofoms.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}