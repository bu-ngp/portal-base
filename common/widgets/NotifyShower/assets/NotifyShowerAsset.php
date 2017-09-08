<?php

namespace common\widgets\NotifyShower\assets;

use yii\web\AssetBundle;

class NotifyShowerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@bower/remarkable-bootstrap-notify/dist';

        $this->js = [
            'bootstrap-notify.min.js',
        ];

        $this->depends = [
            'yii\web\JqueryAsset',
        ];

        parent::init();
    }
}
