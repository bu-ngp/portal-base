<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 10:11
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

/**
 * Абстрактный класс пакета подключаемых скриптов и стилей для PropellerKit.
 *
 * Собирает зависимости от пакетов PropellerKit в зависимости от содержащихся виджетов на странице.
 */
abstract class AssetBundlePropeller extends AssetBundle
{
    private static $widgetsList = [];

    /**
     * Инициализация пакета.
     */
    public function init()
    {
        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        $depends = $this->initDepends();

        foreach ($depends as $class => $propellerDepends) {
            if (in_array($class, self::$widgetsList)) {
                $this->depends = array_unique(array_merge($this->depends, $propellerDepends));
            }
        }

        parent::init();
    }

    /**
     * Добавить виджет для последующего определения зависимостей от пакетов.
     *
     * @param string $class Полное имя класса виджета
     */
    public static function setWidget($class)
    {
        if (!in_array($class, self::$widgetsList)) {
            self::$widgetsList[] = $class;
        }
    }

    /**
     * Абстрактный метод с конфигурацией виджетов и их зависимостей в виде массива.
     *
     * ```php
     *     function initDepends()
     *     {
     *         return [
     *             'common\widgets\Select2\Select2' => [
     *                 'common\widgets\PropellerAssets\ButtonAsset',
     *                 'common\widgets\PropellerAssets\Select2Asset',
     *                 'common\widgets\PropellerAssets\TextFieldSelect2Asset',
     *             ],
     *             'yii\bootstrap\NavBar' => [
     *                 'common\widgets\PropellerAssets\NavBarAsset',
     *             ],
     *         ];
     *     }
     * ```
     *
     * @return array
     */
    abstract function initDepends();
}