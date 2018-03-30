<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 05.10.2017
 * Time: 9:43
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для Radio кнопок.
 */
class RadioAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm/propellerkit';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'components/radio/css/radio.css',
    ];

    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'components/radio/js/radio.js',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}