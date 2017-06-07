<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.05.2017
 * Time: 14:55
 */

namespace common\widgets\GridView\services;


use common\widgets\GridView\GridView;
use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\web\View;

class GWExportGrid
{
    private $config;

    public static function lets($config)
    {
        return new self($config);
    }

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function prepareConfig(array &$jsScripts)
    {
        $this->prepareJS($jsScripts);
        $this->makeButtonOnToolbar();
        return $this->config;
    }


    protected function prepareJS(&$jsScripts)
    {
        $options = [];

        $json_options = json_encode($options, JSON_UNESCAPED_UNICODE);

        $jsScripts[] = "$('#{$this->config['id']}-pjax').wkexport($json_options);";
    }

    protected function makeButtonOnToolbar()
    {
        $toolbar = Html::a(Yii::t('wk-widget-gridview', 'Export'), '#',
            [
                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger wk-btn-exportGrid',
                'style' => 'text-align: right;',
            ]);

        $this->config['toolbar'][1]['content'] .= $toolbar;

    }
}