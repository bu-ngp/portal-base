<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 10:11
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

abstract class AssetBundlePropeller extends AssetBundle
{
    private static $widgetsList = [];

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

    public static function setWidget($class)
    {
        if (!in_array($class, self::$widgetsList)) {
            self::$widgetsList[] = $class;
        }
    }

    abstract function initDepends();
}