<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 28.10.2017
 * Time: 9:14
 */

namespace common\widgets\Documenter\assets;

class DocumenterAsset extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        $this->css = [
            'css/documenter.css',
        ];

        $this->js = [
            'js/documenter.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }

}