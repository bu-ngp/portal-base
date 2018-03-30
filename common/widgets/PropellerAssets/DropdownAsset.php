<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 16:39
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для DropDown.
 */
class DropdownAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm/propellerkit';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'components/dropdown/css/dropdown.css',
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
    ];
}