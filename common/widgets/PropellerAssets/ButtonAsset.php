<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 10:50
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class ButtonAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/button/css/button.css',
        ];

        $this->js = [
            'components/button/js/ripple-effect.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}