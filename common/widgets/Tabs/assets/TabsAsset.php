<?php
namespace common\widgets\Tabs\assets;

use yii\web\AssetBundle;

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.10.2017
 * Time: 11:03
 */
class TabsAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/tab/css/tab.css',
        ];

        $this->js = [
            'components/tab/js/tab-scrollable.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}