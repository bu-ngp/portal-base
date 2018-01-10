<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 10.01.2018
 * Time: 15:55
 */

namespace common\widgets\SystemInfo\assets;


use yii\web\AssetBundle;

class SystemInfoAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->css = [
            'css/SystemInfo.css',
        ];
        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}