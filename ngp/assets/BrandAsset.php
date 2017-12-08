<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.11.2017
 * Time: 11:10
 */

namespace ngp\assets;


use yii\web\AssetBundle;

class BrandAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $css = [
        'css/brand.css',
    ];
}