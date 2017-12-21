<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.05.2017
 * Time: 10:28
 */

namespace common\widgets\GridView\services;


use common\widgets\GridView\GridView;
use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\grid\Column;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;

class GWCustomizeDialog
{
    /** @var GridView */
    private $gridView;

    public static function lets($gridView)
    {
        return new self($gridView);
    }

    public function __construct($gridView)
    {
        $this->gridView = $gridView;
    }

    public function prepareConfig()
    {
        $this->prepareJS();
        $this->makeButtonOnToolbar();
        $this->prepareColumns();
        $this->preparePager();
        return $this;
    }

    public function makeColumnsContent()
    {
        $visible = '';
        $hidden = '';
        array_walk($this->gridView->columns, function ($column) use (&$visible, &$hidden) {
            $attributeLabel = $column['attribute'];

            if ($this->gridView->filterModel instanceof Model) {
                $attributeLabel = $this->gridView->filterModel->getAttributeLabel($column['attribute']);
            } elseif (isset($this->gridView->dataProvider->query)) {
                $attributeLabel = $this->gridView->dataProvider->query->getAttributeLabel($column['attribute']);
            }

            /** @var $column DataColumn */
            if ($column['visible']) {
                if (empty($column['options']['wk-widget'])) {
                    $visible .= '<a role="option" aria-grabbed="false" draggable="true" class="list-group-item" wk-hash="' . $column['headerOptions']['wk-hash'] . '">' . $attributeLabel . '</a>';
                }
            } else {
                $hidden .= '<a role="option" aria-grabbed="false" draggable="true" class="list-group-item" wk-hash="' . $column['headerOptions']['wk-hash'] . '">' . $attributeLabel . '</a>';
            }
        });

        $pagerValue = $this->gridView->dataProvider->getPagination()->pageSize;

        $this->gridView->columns = array_filter($this->gridView->columns, function ($column) {
            /** @var $column Column */
            return $column['visible'];
        });

        $this->gridView->panel['before'] .= <<<EOT
        <div class="{$this->gridView->id}-wk-customize-dialog-content" style="display: none;">
            <div class="wk-customize-dialog-pagerValue">$pagerValue</div>
            <div class="wk-customize-dialog-visible-columns">$visible</div>
            <div class="wk-customize-dialog-hidden-columns">$hidden</div>
        </div>
EOT;
    }

    protected function prepareJS()
    {
        $options = [
            'titleDialogMessage' => Yii::t('wk-widget-gridview', 'Customize Dialog'),
            'rowsPerPageMessage' => Yii::t('wk-widget-gridview', 'Rows Per Page'),
            'visibleColumnsMessage' => Yii::t('wk-widget-gridview', 'Visible Columns'),
            'hiddenColumnsMessage' => Yii::t('wk-widget-gridview', 'Hidden Columns'),
            'rowsPerPageDescriptionMessage' => Yii::t('wk-widget-gridview', 'Enter the number of records on the grid from 10 to 100'),
            'visibleColumnsDescriptionMessage' => Yii::t('wk-widget-gridview', 'Drag to the left of the column that you want to see in the grid in a specific order'),
            'saveChangesMessage' => Yii::t('wk-widget-gridview', 'Save changes'),
            'cancelMessage' => Yii::t('wk-widget-gridview', 'Cancel'),
            'resetSortMessage' => Yii::t('wk-widget-gridview', 'Reset Sort'),
            'resetMessage' => Yii::t('wk-widget-gridview', 'Reset'),
            'resetConfirmTitleMessage' => Yii::t('wk-widget-gridview', 'Confirm'),
            'resetConfirmMessage' => Yii::t('wk-widget-gridview', 'Reset Columns. Are you sure?'),
            'resetSortConfirmTitleMessage' => Yii::t('wk-widget-gridview', 'Confirm'),
            'resetSortConfirmMessage' => Yii::t('wk-widget-gridview', 'Reset Sort Grid. Are you sure?'),
            'confirmCloseMessage' => Yii::t('wk-widget-gridview', 'Close'),
            'confirmOKMessage' => Yii::t('wk-widget-gridview', 'OK'),
            'alertOKMessage' => Yii::t('wk-widget-gridview', 'OK'),
            'validatePagerMessage' => Yii::t('wk-widget-gridview', 'Rows per page must be from 10 to 100'),
            'validateColumnsMessage' => Yii::t('wk-widget-gridview', 'Visible columns cannot empty'),
        ];

        $json_options = json_encode($options, JSON_UNESCAPED_UNICODE);

        $this->gridView->registerJs("$('#{$this->gridView->id}-pjax').wkcustomize($json_options)");
    }

    protected function makeButtonOnToolbar()
    {
        $button = Html::a(Yii::t('wk-widget-gridview', 'Customize'), '#',
            [
                'class' => 'btn btn-xs pmd-btn-flat pmd-ripple-effect btn-default wk-btn-customizeDialog',
                'data-pjax' => '0',
                'tabindex' => '-1',
            ]);

        $this->gridView->customButtonsInternal[] = $button;
    }

    protected function prepareColumns()
    {
        if ($_COOKIE[$this->gridView->id]) {
            $cookieColumns = $this->CookieColumns();
            $this->gridView->columns = array_merge($cookieColumns->hidden, $cookieColumns->visible);
        }
    }

    protected function CookieColumns()
    {
        $visible = [];
        $hidden = [];
        $columns = $this->gridView->columns;

        $process = [];

        foreach ($columns as $column) {
            /** @var $column Column */
            if (ArrayHelper::getValue($column, 'options.wk-widget', false)) {
                $visible[] = $column;
            } else {
                $process[] = $column;
            }
        }

        if ($_COOKIE[$this->gridView->id]) {
            $cookieOptions = json_decode($_COOKIE[$this->gridView->id]);

            if (property_exists($cookieOptions, 'visible')
                && !empty($cookieOptions->visible)
            ) {
                foreach ($cookieOptions->visible as $colCookie) {
                    $filterCol = array_filter($process, function ($col) use ($colCookie) {
                        /** @var $col Column */
                        return $col['headerOptions']['wk-hash'] === $colCookie;
                    });

                    $keyFilterCol = array_keys($filterCol)[0];

                    if (!empty($filterCol)) {
                        $filterCol[$keyFilterCol]['visible'] = true;
                        $visible[] = $filterCol[$keyFilterCol];
                        unset($process[array_keys($filterCol)[0]]);
                    }
                }

                array_walk($process, function (&$column) {
                    /** @var $column Column */
                    $column['visible'] = false;
                });
            }
        }

        array_map(function ($column) use (&$visible, &$hidden) {
            /** @var $column Column */
            array_push($column['visible'] ? $visible : $hidden, $column);
        }, $process);

        return (object)[
            'visible' => $visible,
            'hidden' => $hidden,
        ];
    }

    protected function preparePager()
    {
        if ($_COOKIE[$this->gridView->id]) {
            $cookieOptions = json_decode($_COOKIE[$this->gridView->id]);

            if (property_exists($cookieOptions, 'pager') && $cookieOptions->pager >= 10 && $cookieOptions->pager <= 100) {
                $this->gridView->dataProvider->getPagination()->pageSize = $cookieOptions->pager;
            }

            if (property_exists($cookieOptions, 'sort')) {
                if (substr($cookieOptions->sort, 0, 1) === '-') {
                    $cookieOptions->sort = substr($cookieOptions->sort, 1);
                    $direction = SORT_DESC;
                } else {
                    $direction = SORT_ASC;
                }

                $this->gridView->dataProvider->getSort()->defaultOrder = [$cookieOptions->sort => $direction];
            }
        }
    }

}