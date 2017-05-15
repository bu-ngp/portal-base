<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 20:00
 */

namespace common\widgets\CardList\assets;


use yii\web\AssetBundle;

class MasonryAssets extends AssetBundle
{
    public $sourcePath = '@npm';
    public $css = [

    ];
    public $js = [
        'masonry-layout/dist/masonry.pkgd.min.js',
        'imagesloaded/imagesloaded.pkgd.min.js',
    ];
    public $depends = [
    ];
}