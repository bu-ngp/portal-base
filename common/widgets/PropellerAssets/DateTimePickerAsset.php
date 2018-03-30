<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 05.10.2017
 * Time: 11:38
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для выбора дат из календаря.
 */
class DateTimePickerAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'propellerkit/components/datetimepicker/css/bootstrap-datetimepicker.css',
        'propellerkit/components/datetimepicker/css/pmd-datetimepicker.css',
        'material-design-icons/iconfont/material-icons.css',
    ];

    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'moment/min/moment-with-locales.min.js',
        'propellerkit/components/datetimepicker/js/bootstrap-datetimepicker.js',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\widgets\PropellerAssets\TextFieldAsset',
    ];
}