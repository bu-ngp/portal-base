<?php

namespace common\widgets\GridView;

use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[GridView]]
 */
class GridViewAsset extends AssetBundle
{
    /**
     * @var array Набор css стилей
     */
    public $css = [
        'css/gridview.css',
    ];

    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'js/wkgridview.js',
        'js/gridselected2storage.js',
        'js/wkcustomize.js',
        'js/wkfilter.js',
        'js/wkexport.js',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
        'common\widgets\GridView\SortableAsset',
        'common\widgets\wkdialog\WkDialogAsset',
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
