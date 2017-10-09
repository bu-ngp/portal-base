<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 13:09
 */

namespace common\widgets\Select2;


use common\widgets\PropellerAssets\Select2Asset;
use common\widgets\PropellerAssets\TextFieldAsset;
use yii\bootstrap\Html;
use yii\bootstrap\InputWidget;
use yii\bootstrap\Widget;
use yii\web\View;

class Select2 extends \kartik\select2\Select2
{
    public $theme = self::THEME_BOOTSTRAP;
    public $queryCallback;

//    public $data;
//    public $selectClass = 'select-with-search form-control pmd-select2';

    public function run()
    {
        $this->registerWKAssets1();

        parent::run();

        $this->registerWKAssets2();
    }

    protected function registerWKAssets1()
    {
        $view = $this->getView();

        TextFieldAsset::register($view);

    }

    protected function registerWKAssets2()
    {
        $view = $this->getView();

//        Select2Asset::register($view);
        Select2Asset::register($view);

//
//
//        $view->registerJs(<<<EOT
//$('#{$this->id}').select2({theme: "bootstrap"});
//EOT
//        );
    }
//
//    public function init()
//    {
//
//        echo Html::dropDownList('name', '', $this->data, ['id' => $this->id, 'class' => $this->selectClass]);
//    }


}