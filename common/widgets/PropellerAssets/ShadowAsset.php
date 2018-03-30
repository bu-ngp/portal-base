<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 16:40
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для теней HTML элементов.
 */
class ShadowAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm/propellerkit';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'components/shadow/css/shadow.css',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}