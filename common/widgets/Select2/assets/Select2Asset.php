<?php

namespace common\widgets\Select2\assets;

/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 13:54
 */
class Select2Asset extends \yii\web\AssetBundle
{
    public function init()
    {
        $this->sourcePath = __DIR__;

        $this->js = [
            'js/wkselect2.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            //'kartik\select2\Select2Asset',
        ];

        parent::init();
    }
}