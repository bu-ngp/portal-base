<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 23.12.2017
 * Time: 18:20
 */

namespace common\widgets\FixButtonBackward\assets;


use yii\web\AssetBundle;

class FixButtonBackwardAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__;

        $this->css = [
            'css/wkFixButtonBackward.css',
        ];

        $this->js = [
            'js/wkFixButtonBackward.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'rmrevin\yii\fontawesome\AssetBundle',
        ];

        parent::init();
    }
}