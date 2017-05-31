<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 25.05.2017
 * Time: 10:54
 */

namespace common\widgets\GridView\assets;


use yii\web\AssetBundle;

class SerializeJSONAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@bower';
        $this->css = [];

        $this->js = [
            'jquery.serializeJSON/jquery.serializejson.min.js',
        ];

        $this->depends = [
            'yii\web\JqueryAsset',
        ];

        parent::init();
    }
}