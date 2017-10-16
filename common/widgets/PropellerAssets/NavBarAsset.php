<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 16:31
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class NavBarAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/navbar/css/navbar.css',
            'components/dropdown/css/dropdown.css',
            'components/shadow/css/shadow.css',
        ];

        $this->js = [
            'components/dropdown/js/dropdown.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'common\widgets\PropellerAssets\ButtonAsset',
            'common\widgets\PropellerAssets\ListAsset',
        ];

        parent::init();
    }
}