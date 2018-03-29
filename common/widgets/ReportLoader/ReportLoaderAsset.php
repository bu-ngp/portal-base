<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.06.2017
 * Time: 9:38
 */

namespace common\widgets\ReportLoader;


use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[ReportLoader]]
 */
class ReportLoaderAsset extends AssetBundle
{
    /**
     * @var array Набор css стилей
     */
    public $css = [
        'css/reportloader.css',
    ];
    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'js/wkreportloader.js',
    ];
    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
        'common\widgets\ReportLoader\ProgressbarAsset',
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