<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 15:51
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class CheckboxAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/checkbox/css/checkbox.css',
        ];

        $this->js = [
            'components/checkbox/js/checkbox.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}