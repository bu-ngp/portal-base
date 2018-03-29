<?php

namespace common\widgets\GridViewModal\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class GridViewModalAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->css = [
            'css/gridviewmodal.css',
        ];

        $this->depends = [
            'common\widgets\GridView\GridViewAsset',
            'common\widgets\GridSelected2TextInput\GridSelected2TextInputAsset',
        ];

        parent::init();
    }
}
