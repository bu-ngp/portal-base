<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 16.10.2017
 * Time: 10:14
 */

namespace common\widgets\PropellerAssets;


class PropellerAsset extends AssetBundlePropeller
{
    function initDepends()
    {
        return [
            'common\widgets\Select2\Select2' => [
                'common\widgets\PropellerAssets\ButtonAsset',
                'common\widgets\PropellerAssets\Select2Asset',
                'common\widgets\PropellerAssets\TextFieldSelect2Asset',
            ],
            'kartik\select2\Select2' => [
                'common\widgets\PropellerAssets\ButtonAsset',
                'common\widgets\PropellerAssets\Select2Asset',
                'common\widgets\PropellerAssets\TextFieldAsset',
            ],
            'common\widgets\CardList\CardList' => [
                'common\widgets\PropellerAssets\ButtonAsset',
                'common\widgets\PropellerAssets\TextFieldAsset',
                'common\widgets\PropellerAssets\CardAsset',
                'common\widgets\PropellerAssets\ShadowAsset',
            ],
            'common\widgets\GridView\GridView' => [
                'common\widgets\PropellerAssets\ButtonAsset',
                'common\widgets\PropellerAssets\TextFieldAsset',
                'common\widgets\PropellerAssets\ListAsset',
                'common\widgets\PropellerAssets\ModalAsset',
                'common\widgets\PropellerAssets\TabAsset',
                'common\widgets\PropellerAssets\CheckboxAsset',
            ],
            'common\widgets\ReportLoader\ReportLoader' => [
                'common\widgets\PropellerAssets\ButtonAsset',
                'common\widgets\PropellerAssets\ModalAsset',
            ],
            'common\widgets\Documenter\Documenter' => [
                'common\widgets\PropellerAssets\CardAsset',
                'common\widgets\PropellerAssets\TabAsset',
            ],
            'yii\bootstrap\NavBar' => [
                'common\widgets\PropellerAssets\NavBarAsset',
            ],
            'toggleswitch' => [
                'common\widgets\PropellerAssets\ToggleSwitchAsset',
            ],
            'checkbox' => [
                'common\widgets\PropellerAssets\CheckboxAsset',
            ],
            'input' => [
                'common\widgets\PropellerAssets\TextFieldAsset',
            ],
            'radiolist' => [
                'common\widgets\PropellerAssets\RadioAsset',
            ],
            'datetimepicker' => [
                'common\widgets\PropellerAssets\DateTimePickerAsset',
            ],
        ];
    }
}