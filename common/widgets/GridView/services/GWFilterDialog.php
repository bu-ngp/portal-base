<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.05.2017
 * Time: 14:55
 */

namespace common\widgets\GridView\services;


use Yii;
use yii\bootstrap\Html;
use yii\web\View;

class GWFilterDialog
{
    private $config;
    private $configColumns;
    private $columns;

    public static function lets($config)
    {
        return new self($config);
    }

    public function __construct($config)
    {
        $this->config = $config;
        $this->configColumns = $config['columns'];
        $this->columns = [];
    }

    public function prepareConfig(array &$jsScripts)
    {
        $this->prepareJS($jsScripts);
        $this->makeButtonOnToolbar();
        return $this->config;
    }

    /**
     * @param array $filterOptions
     * @param View $view
     */
    public function makeFilterContent(array $filterOptions, $view)
    {

        echo <<<EOT
        <div class="wk-filter-dialog-content" style="display: none;>
            {$view->render($filterOptions['filterView'], ['filterModel' => $filterOptions['filterModel']])}
        </div>
EOT;
    }

    protected function prepareJS(&$jsScripts)
    {
        $options = [
            'titleDialogMessage' => Yii::t('wk-widget-gridview', 'Additional Filter'),
            'applyButtonMessage' => Yii::t('wk-widget-gridview', 'Apply'),
            'cancelButtonMessage' => Yii::t('wk-widget-gridview', 'Cancel'),
            'resetButtonMessage' => Yii::t('wk-widget-gridview', 'Reset Filter'),
            'searchMessage' => Yii::t('wk-widget-gridview', 'Search'),
            'resetConfirmMessage' => Yii::t('wk-widget-gridview', 'Reset Filter. Are you sure?'),
        ];

        $json_options = json_encode($options, JSON_UNESCAPED_UNICODE);

        $jsScripts[] = "$('#{$this->config['id']}-pjax').wkfilter($json_options);";
    }


    protected function makeButtonOnToolbar()
    {
        $toolbar = Html::a(Yii::t('wk-widget-gridview', 'Filter'), '#',
            [
                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary wk-btn-filterDialog',
                'style' => 'text-align: right;',
            ]);

        $this->config['toolbar'][1]['content'] .= $toolbar;
    }
}