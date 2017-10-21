<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 21.10.2017
 * Time: 17:02
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class ToggleSwitchAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/toggle-switch/css/toggle-switch.css',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}