<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.05.2017
 * Time: 14:55
 */

namespace common\widgets\GridView\services;


use Yii;
use yii\base\Model;
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
        if (Yii::$app->request->isAjax) {
            $panelBefore = '';

            if ($_COOKIE[$this->config['id']]) {
                $cookieOptions = json_decode($_COOKIE[$this->config['id']], true);

                if (!empty($cookieOptions['_filter'])) {
                    $filterOptions['filterModel']->load($cookieOptions['_filter']);

                    $panelBefore = $this->getOutputString($filterOptions['filterModel']);
                }
            }

            echo <<<EOT
        <div class="wk-filter-dialog-content" style="display: none;>
            {$view->render($filterOptions['filterView'], ['filterModel' => $filterOptions['filterModel']])}
        </div>
        $panelBefore
EOT;
        }
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

    protected function getOutputString(Model $filterModel)
    {
        $output = '';
        foreach ($filterModel->attributes as $attr => $value) {
            if (substr($attr, strlen($attr) - 5) === '_mark' && $value === '1') {
                $output .= '<span class="wk-filter-output-value">' . $filterModel->getAttributeLabel($attr) . '</span>; ';
            } elseif (!empty($value)) {
                $output .= '<span class="wk-filter-output-name">' . $filterModel->getAttributeLabel($attr) . '</span> = "<span class="wk-filter-output-value">' . $value . '</span>; ';
            }
        }

        if (!empty($output)) {
            $output = '<div class="wk-filter-output"><div><span><b>Доп. фильтр: </b>' . $output . '</span></div><div><button aria-label="Close" data-dismiss="alert" class="close wk-filter-output-close" type="button"><span aria-hidden="true">×</span></button></div></div>';
        }

        return $output;
    }
}