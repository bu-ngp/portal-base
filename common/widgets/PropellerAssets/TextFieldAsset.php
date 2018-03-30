<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 15:01
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для текстовых полей.
 */
class TextFieldAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm/propellerkit';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'components/textfield/css/textfield.css',
    ];

    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'components/textfield/js/textfield.js',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}