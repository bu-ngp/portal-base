<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 15:46
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Пакет jquery плагина PropellerKit для карт-плиток.
 */
class CardAsset extends AssetBundle
{
    /**
     * @var string Источник ресурсов
     */
    public $sourcePath = '@npm/propellerkit';

    /**
     * @var array Набор css стилей
     */
    public $css = [
        'components/card/css/card.css',
    ];

    /**
     * @var array Зависимости
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}