<?php

namespace common\widgets\gridSelected2TextInput;

use yii\web\AssetBundle;

/**
 * Пакет скриптов для jquery плагина `gridselected2textinput.js`.
 */
class GridSelected2TextInputAsset extends AssetBundle
{
    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'gridselected2textinput.js',
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
