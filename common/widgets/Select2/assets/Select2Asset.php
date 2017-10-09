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
        $this->sourcePath = '@bower/select2';
        $this->css = [
            'dist/css/select2.min.css',
        ];

        $this->js = [
            'dist/js/select2.full.min.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}