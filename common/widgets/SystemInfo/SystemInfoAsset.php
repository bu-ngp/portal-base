<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 10.01.2018
 * Time: 15:55
 */

namespace common\widgets\SystemInfo;

use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[SystemInfo]]
 */
class SystemInfoAsset extends AssetBundle
{
    /**
     * @var array Набор css стилей
     */
    public $css = [
        'css/SystemInfo.css',
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