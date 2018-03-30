<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 14:00
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для [[\common\widgets\Select2\Select2]].
 */
class TextFieldSelect2Asset extends AssetBundle
{
    /**
     * @var array Набор css стилей
     */
    public $css = [
        'css/textfieldselect2.css',
    ];

    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'js/textfieldselect2.js',
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