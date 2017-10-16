<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 15:50
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class ModalAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/modal/css/modal.css',
        ];

        $this->js = [
            'components/modal/js/modal.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}