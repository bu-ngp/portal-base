<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 13:54
 */

namespace common\widgets\Select2;

use yii\web\AssetBundle;

/**
 * Пакет стилей и скриптов для виджета [[Select2]]
 */
class Select2Asset extends AssetBundle
{
    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'js/wkselect2.js',
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