<?php
namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 05.10.2017
 * Time: 9:43
 */
class RadioAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/radio/css/radio.css',
        ];

        $this->js = [
            'components/radio/js/radio.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}