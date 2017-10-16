<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.06.2017
 * Time: 9:38
 */

namespace common\widgets\ReportLoader\assets;


use yii\web\AssetBundle;

class ReportLoaderAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->css = [
            'css/reportloader.css',
        ];

        $this->js = [
            'js/wkreportloader.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'rmrevin\yii\fontawesome\AssetBundle',
            'common\widgets\ReportLoader\assets\ProgressbarAsset',
        ];

        parent::init();
    }
}