<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 13:18
 */

namespace common\widgets\PropellerAssets;


use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm/propellerkit';
        $this->css = [
            'components/select2/css/pmd-select2.css',
        ];

        $this->js = [
            'components/select2/js/pmd-select2.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}