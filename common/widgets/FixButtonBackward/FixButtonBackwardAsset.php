<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 23.12.2017
 * Time: 18:20
 */

namespace common\widgets\FixButtonBackward;

use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[FixButtonBackward]]
 */
class FixButtonBackwardAsset extends AssetBundle
{
    /**
     * @var array Набор css стилей
     */
    public $css = [
        'css/wkFixButtonBackward.css',
    ];
    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'js/wkFixButtonBackward.js',
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