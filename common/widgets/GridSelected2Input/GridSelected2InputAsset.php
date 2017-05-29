<?php

namespace common\widgets\gridSelected2Input;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class GridSelected2InputAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->js = [
            'gridselected2textinput.js',
        ];

        $this->depends = [
            'yii\web\JqueryAsset',
        ];

        parent::init();
    }
}
