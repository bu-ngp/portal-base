<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class PropellerAsset extends AssetBundle
{
    public $sourcePath = '@npm/propellerkit';
    public $css = [
        //   'components/typography/css/typography.css',
        'components/button/css/button.css',
        'components/icons/css/google-icons.css',
        'components/card/css/card.css',
        'components/navbar/css/navbar.css',
        'components/dropdown/css/dropdown.css',
        'components/list/css/list.css',
        'components/modal/css/modal.css',
        'components/textfield/css/textfield.css',
    ];
    public $js = [
        'components/sidebar/js/sidebar.js',
        'components/dropdown/js/dropdown.js',
        'components/button/js/ripple-effect.js',
        'components/modal/js/modal.js',
        'components/textfield/js/textfield.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
