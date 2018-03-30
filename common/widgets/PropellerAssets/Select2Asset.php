<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 13:18
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для Select2.
 */
class Select2Asset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm/propellerkit';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'components/select2/css/pmd-select2.css',
    ];

    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'components/select2/js/pmd-select2.js',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}