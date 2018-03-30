<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 21.10.2017
 * Time: 17:02
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для ToggleSwitch.
 */
class ToggleSwitchAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm/propellerkit';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'components/toggle-switch/css/toggle-switch.css',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}