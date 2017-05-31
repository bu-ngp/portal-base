<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 25.05.2017
 * Time: 10:54
 */

namespace common\widgets\GridView\assets;


use yii\web\AssetBundle;

class SortableAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@npm';
        $this->css = [];

        $this->js = [
            'jquery-ui/ui/version.js',
            'jquery-ui/ui/widget.js',
            'jquery-ui/ui/ie.js',
            'jquery-ui/ui/data.js',
            'jquery-ui/ui/scroll-parent.js',
            'jquery-ui/ui/widgets/mouse.js',
            'jquery-ui/ui/widgets/sortable.js',
            'jquery-ui/ui/disable-selection.js',
            'jquery-ui/ui/safe-blur.js',
            'jquery-ui/ui/safe-active-element.js',
            'jquery-ui/ui/plugin.js',
            'jquery-ui/ui/widgets/draggable.js',
        ];

        $this->depends = [
            'yii\web\JqueryAsset',
        ];

        parent::init();
    }
}