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
    public $sourcePath = '@npm/masonry-layout';
    public $css = [

    ];
    public $js = [
        'dist/masonry.pkgd.min.js',
    ];
    public $depends = [
    ];
}