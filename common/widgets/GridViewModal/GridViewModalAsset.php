<?php

namespace common\widgets\GridViewModal;

use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[GridViewModal]]
 */
class GridViewModalAsset extends AssetBundle
{
    /**
     * @var array Набор css стилей
     */
    public $css = [
        'css/gridviewmodal.css',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'common\widgets\GridView\GridViewAsset',
        'common\widgets\GridSelected2TextInput\GridSelected2TextInputAsset',
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
