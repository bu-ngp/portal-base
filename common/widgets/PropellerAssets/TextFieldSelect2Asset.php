<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 14:00
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class TextFieldSelect2Asset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->css = [
            'assets/css/textfieldselect2.css',
        ];

        $this->js = [
            'assets/js/textfieldselect2.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }

}