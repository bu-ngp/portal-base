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
            $output = '<div class="wk-filter-output"><div><span><b>Доп. фильтр: </b>' . $output . '</span></div><div><button aria-label="Close" data-dismiss="alert" class="close wk-filterDialog-btn-close" type="button"><span aria-hidden="true">×</span></button></div></div>';
        }

        return $output;
    }

    public function makeFilter(GridView $gridView)
    {
        if (Yii::$app->request->isAjax && $this->config['dataProvider'] instanceof ActiveDataProvider) {
            $filterMessage = '';

            /** @var Model $filterModel */
            $filterModel = $gridView->filterDialog['filterModel'];

            if ($_COOKIE[$this->config['id']]) {
                $cookieOptions = json_decode($_COOKIE[$this->config['id']], true);

                parse_str($cookieOptions['_filter'], $filterParams);

                if (is_array($filterParams)
                    && count($filterParams) > 0
                    && $filterModel->load($filterParams)
                ) {
                    $filterMessage = $this->getOutputString($filterModel);
                    $this->applyQueryConditions($filterModel);
                }
            }

            $gridView->panel['before'] .= $this->makeFilterContent($gridView->getView(), $gridView->filterDialog['filterView'], $filterModel) . $filterMessage;
        }
    }

    protected function makeFilterContent(View $view, $filterView, Model $filterModel)
    {
        return <<<EOT
                <div class="wk-filter-dialog-content" style="display: none;">
                    {$view->render($filterView, ['filterModel' => $filterModel])}
                </div>
EOT;
    }

    protected function applyQueryConditions(Model $filterModel)
    {
        /** @var ActiveQuery $query */
        $query = $this->config['dataProvider']->query;

        $alias = 't' . time();

        $query->alias($alias);

        foreach (array_keys(get_object_vars($filterModel)) as $propertyFilter) {
            $methodFilter = 'filter_' . $propertyFilter;
            if (!empty($filterModel->$propertyFilter) && method_exists($filterModel, $methodFilter)) {
                $query->andWhere(['exists',
                    $filterModel->$methodFilter($alias)
                ]);
            }
        }
    }
}