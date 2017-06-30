<?php

namespace common\widgets\Breadcrumbs\assets;

use yii\web\AssetBundle;

class BreadcrumbsAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__;

        $this->js = [
            'js/wkbreadcrumbs.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}
