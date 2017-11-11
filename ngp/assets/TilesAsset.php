<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.11.2017
 * Time: 11:10
 */

namespace ngp\assets;


use yii\web\AssetBundle;

class TilesAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $css = [
        'css/tiles.css',
    ];

    public $js = [
        'js/tiles.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}