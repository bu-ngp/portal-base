<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 15:53
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class ListAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/list/css/list.css',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}