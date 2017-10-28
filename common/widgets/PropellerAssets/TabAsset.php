<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 15:52
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class TabAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm';
        $this->css = [
            'material-design-icons/iconfont/material-icons.css',
            'propellerkit/components/tab/css/tab.css',
        ];

        $this->js = [
            'propellerkit/components/tab/js/tab-scrollable.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}