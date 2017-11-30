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
use yii\db\ActiveQuery;
use yii\web\View;

class GWFilterDialog
{
    /** @var GridView */
    private $gridView;
    private $columns;
    private $additionalFilter = '';

    public static function lets(GridView $gridView)
    {
        if (!($gridView->filterDialog instanceof GWFilterDialogConfiguration)) {
            throw new \Exception('filterDialog must be GWFilterDialogConfiguration class');
        }

        if (!$gridView->filterDialog->isEnable()) {
            throw new \Exception('GWFilterDialogConfiguration must be enable');
        }

        return new self($gridView);
    }

    public function __construct(GridView $gridView)
    {
        $this->gridView = $gridView;
        $this->columns = [];
    }

    public function prepareConfig()
    {
        $this->prepareJS();
        $this->makeButtonOnToolbar();
        return $this;
    }

    public function getAdditionFilterString()
    {
        return $this->additionalFilter;
    }

    protected function prepareJS()
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

        $this->gridView->registerJs("$('#{$this->gridView->id}-pjax').wkfilter($json_options);");
    }


    protected function makeButtonOnToolbar()
    {
        $button = Html::a(Yii::t('wk-widget-gridview', 'Filter'), '#',
            [
                'class' => 'btn btn-xs pmd-btn-flat pmd-ripple-effect btn-primary wk-btn-filterDialog',
                'tabindex' => '-1',
                'role' => 'menuitem',
            ]);

        $this->gridView->customButtons[] = $button;
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
            $output = '<div class="wk-filter-output"><div><span><b>' . Yii::t('wk-widget-gridview', 'Add. filter: ') . '</b>' . $output . '</span></div><div><button aria-label="Close" data-dismiss="alert" class="close wk-filterDialog-btn-close" type="button"><span aria-hidden="true"><i class="fa fa-close"></i></span></button></div></div>';
        }

        $this->additionalFilter = mb_substr(strip_tags($output), mb_strlen(Yii::t('wk-widget-gridview', 'Add. filter: '), 'UTF-8'), null, 'UTF-8');

        return $output;
    }

    public function makeFilter()
    {
        if (Yii::$app->request->isAjax) {
            $filterMessage = '';

            /** @var Model $filterModel */
            $filterModel = $this->gridView->filterDialog->getFilterModel();

            if ($_COOKIE[$this->gridView->id]) {
                $cookieOptions = json_decode($_COOKIE[$this->gridView->id], true);

                parse_str($cookieOptions['_filter'], $filterParams);

                if (is_array($filterParams)
                    && count($filterParams) > 0
                    && $filterModel->load($filterParams)
                ) {
                    $filterMessage = $this->getOutputString($filterModel);
                    $this->applyQueryConditions($filterModel);
                }
            }

            $this->gridView->panel['before'] .= $this->makeFilterContent($this->gridView->getView(), $this->gridView->filterDialog->getFilterView(), $filterModel) . $filterMessage;
        }

        return $this->additionalFilter;
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
        $query = $this->gridView->dataProvider->query;

        $alias = 't' . time();

        // $query->alias($alias);

        foreach (array_keys(get_object_vars($filterModel)) as $propertyFilter) {
            $methodFilter = 'filter_' . $propertyFilter;
            if (!empty($filterModel->$propertyFilter) && method_exists($filterModel, $methodFilter)) {
                /** @var ActiveQuery $subQuery */
                $subQuery = $filterModel->$methodFilter($this->getTableName($query), $alias);
                $subQuery->alias($alias);

                $query->andWhere(['exists',
                    $subQuery
                ]);
            }
        }
    }

    protected function getTableName(ActiveQuery $query)
    {
        if ($query->from) {
            return is_array($query->from) ? key($query->from) : $query->from;
        }

        return call_user_func([$query->modelClass, 'tableName']);
    }
}