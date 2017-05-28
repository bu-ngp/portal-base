<?php

namespace common\widgets\wkdialog;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class WkDialogAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->js = [
            'wkdialog.js',
        ];

        $this->depends = [
            'yii\web\JqueryAsset',
        ];

        parent::init();
    }
}
