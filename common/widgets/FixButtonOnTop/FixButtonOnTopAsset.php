<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 23.12.2017
 * Time: 17:37
 */

namespace common\widgets\FixButtonOnTop;

use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[FixButtonOnTop]]
 */
class FixButtonOnTopAsset extends AssetBundle
{
    /**
     * @var array Набор css стилей
     */
    public $css = [
        'css/wkFixButtonOnTop.css',
    ];
    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'js/wkFixButtonOnTop.js',
    ];
    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
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