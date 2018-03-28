<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 26.12.2017
 * Time: 16:01
 */

namespace common\widgets\HeaderPanel;


use yii\web\AssetBundle;

/**
 * Пакет стилей для [[HeaderPanel]]
 */
class HeaderPanelAsset extends AssetBundle
{
    /**
     * @var array
     */
    public $css = [
        'css/HeaderPanel.css',
    ];
    /**
     * @var array
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