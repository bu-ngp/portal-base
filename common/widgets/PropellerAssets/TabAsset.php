<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 15:52
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для Tab.
 */
class TabAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'material-design-icons/iconfont/material-icons.css',
        'propellerkit/components/tab/css/tab.css',
    ];

    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'propellerkit/components/tab/js/tab-scrollable.js',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}