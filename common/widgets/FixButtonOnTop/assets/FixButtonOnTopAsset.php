<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 23.12.2017
 * Time: 17:37
 */

namespace common\widgets\FixButtonOnTop\assets;


use yii\web\AssetBundle;

class FixButtonOnTopAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__;

        $this->css = [
            'css/wkFixButtonOnTop.css',
        ];

        $this->js = [
            'js/wkFixButtonOnTop.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'rmrevin\yii\fontawesome\AssetBundle',
        ];

        parent::init();
    }
}