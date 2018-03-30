<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 28.10.2017
 * Time: 9:14
 */

namespace common\widgets\Documenter;

use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[Documenter]]
 */
class DocumenterAsset extends AssetBundle
{
    /**
     * @var array Набор css стилей
     */
    public $css = [
        'css/documenter.css',
    ];
    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'js/documenter.js',
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