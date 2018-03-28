<?php

namespace common\widgets\CardList;

use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[CardList]]
 */
class CardListAsset extends AssetBundle
{
    /**
     * @var array
     */
    public $css = [
        'css/cardlist.css',
    ];
    /**
     * @var array
     */
    public $js = [
        'js/wkcardlist.js',
    ];
    /**
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\widgets\CardList\MasonryAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];

    /**
     * Инициализация пакета.
     * ```php
     * $this->sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
     * ```
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
        parent::init();
    }
}
