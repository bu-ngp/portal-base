<?php

namespace common\widgets\Breadcrumbs;

use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[Breadcrumbs]]
 */
class BreadcrumbsAsset extends AssetBundle
{
    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'js/wkbreadcrumbs.js',
    ];
    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
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
