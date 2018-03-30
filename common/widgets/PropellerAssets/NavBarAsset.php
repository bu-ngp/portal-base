<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 16:31
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для меню навигации.
 */
class NavBarAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm/propellerkit';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'components/navbar/css/navbar.css',
        'components/dropdown/css/dropdown.css',
        'components/shadow/css/shadow.css',
    ];

    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'components/dropdown/js/dropdown.js',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\widgets\PropellerAssets\ButtonAsset',
        'common\widgets\PropellerAssets\ListAsset',
    ];
}