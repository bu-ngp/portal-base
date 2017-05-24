<?php

namespace common\widgets\CardList\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class PropellerAsset extends AssetBundle
{
    public $sourcePath = '@npm/propellerkit';
    public $css = [
        'components/button/css/button.css',
        'components/icons/css/google-icons.css',
        'components/card/css/card.css',
        'components/modal/css/modal.css',
        'components/list/css/list.css',
        'components/textfield/css/textfield.css',
    ];
    public $js = [
        'components/button/js/ripple-effect.js',
        'components/modal/js/modal.js',
        'components/textfield/js/textfield.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
