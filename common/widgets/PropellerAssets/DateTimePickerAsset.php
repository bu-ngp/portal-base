<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 05.10.2017
 * Time: 11:38
 */

namespace common\widgets\PropellerAssets;

use yii\web\AssetBundle;

class DateTimePickerAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@npm';
        $this->css = [
            'propellerkit/components/datetimepicker/css/bootstrap-datetimepicker.css',
            'propellerkit/components/datetimepicker/css/pmd-datetimepicker.css',
            'material-design-icons/iconfont/material-icons.css',
        ];

        $this->js = [
            'moment/min/moment-with-locales.min.js',
            'propellerkit/components/datetimepicker/js/bootstrap-datetimepicker.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'common\widgets\PropellerAssets\TextFieldAsset',
        ];

        parent::init();
    }
}