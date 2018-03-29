<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.06.2017
 * Time: 10:57
 */

namespace common\widgets\ReportLoader;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина `progressbar.js` для виджета [[ReportLoader]]
 */
class ProgressbarAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm/progressbar.js/dist';

    /**
     * @var array Набор js скриптов
     */
    public $js = [
        'progressbar.min.js',
    ];
    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}