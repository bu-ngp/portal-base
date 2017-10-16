<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 16:40
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class ShadowAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/shadow/css/shadow.css',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}