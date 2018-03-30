<?php

namespace common\widgets\wkdialog;

use yii\web\AssetBundle;

/**
 * Пакет скриптов для jquery плагина `wkdialog.js`.
 */
class WkDialogAsset extends AssetBundle
{
    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'js/wkdialog.js',
    ];
    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
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
