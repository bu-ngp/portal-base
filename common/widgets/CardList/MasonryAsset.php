<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 20:00
 */

namespace common\widgets\CardList;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина masonry для [[CardList]]
 */
class MasonryAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@npm';
    /**
     * @var array
     */
    public $js = [
        'masonry-layout/dist/masonry.pkgd.min.js',
        'imagesloaded/imagesloaded.pkgd.min.js',
    ];
}