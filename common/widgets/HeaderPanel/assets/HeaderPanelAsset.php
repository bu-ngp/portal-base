<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 26.12.2017
 * Time: 16:01
 */

namespace common\widgets\HeaderPanel\assets;


use yii\web\AssetBundle;

class HeaderPanelAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->css = [
            'css/HeaderPanel.css',
        ];
        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'rmrevin\yii\fontawesome\AssetBundle',
        ];

        parent::init();
    }
}