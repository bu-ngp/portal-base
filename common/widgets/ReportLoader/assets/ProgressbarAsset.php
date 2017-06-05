<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.06.2017
 * Time: 10:57
 */

namespace common\widgets\ReportLoader\assets;


use yii\web\AssetBundle;

class ProgressbarAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@npm/progressbar.js/dist';

        $this->js = [
            'progressbar.min.js',
        ];

        $this->depends = [
            'yii\web\JqueryAsset',
        ];

        parent::init();
    }
}