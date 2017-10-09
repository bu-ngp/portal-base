<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 15:01
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class TextFieldAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/textfield/css/textfield.css',
        ];

        $this->js = [
            'components/textfield/js/textfield.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}