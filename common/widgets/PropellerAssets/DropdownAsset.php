<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 16:39
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class DropdownAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/dropdown/css/dropdown.css',
        ];

        $this->js = [
            'components/dropdown/js/dropdown.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'common\widgets\PropellerAssets\ButtonAsset',
        ];

        parent::init();
    }
}