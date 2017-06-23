<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.05.2017
 * Time: 10:28
 */

namespace common\widgets\GridView\services;


use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\data\DataProviderInterface;

class GWCustomizeDialog
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

    public function prepareConfig(array &$jsScripts, &$panelBeforeTemplate)
    {
        $this->prepareJS($jsScripts);
        $this->makeButtonOnToolbar($panelBeforeTemplate);
        $this->prepareColumns();
        $this->preparePager();
        return $this->config;
    }

    public function makeColumnsContent(DataProviderInterface $dataProvider, Model $filterModel, $id)
    {
        $visible = '';
        $hidden = '';
        array_walk($this->configColumns, function ($column) use (&$visible, &$hidden, $filterModel) {
            if (isset($column['visible']) && $column['visible'] === false) {
                $hidden .= '<a role="option" aria-grabbed="false" draggable="true" class="list-group-item" wk-hash="' . $column['headerOptions']['wk-hash'] . '">' . $filterModel->getAttributeLabel($column['attribute']) . '</a>';
            } else {
                if (empty($column['options']['wk-widget'])) {
                    $visible .= '<a role="option" aria-grabbed="false" draggable="true" class="list-group-item" wk-hash="' . $column['headerOptions']['wk-hash'] . '">' . $filterModel->getAttributeLabel($column['attribute']) . '</a>';
                }
            }
        });

        $pagerValue = $dataProvider->getPagination()->pageSize;
        echo <<<EOT
        <div class="$id-wk-customize-dialog-content" style="display: none;">
            <div class="wk-customize-dialog-pagerValue">$pagerValue</div>
            <div class="wk-customize-dialog-visible-columns">$visible</div>
            <div class="wk-customize-dialog-hidden-columns">$hidden</div>
        </div>
EOT;
    }

    protected function prepareJS(&$jsScripts)
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

        $jsScripts[] = "$('#{$this->config['id']}-pjax').wkcustomize($json_options)";
    }

    protected function makeButtonOnToolbar(&$panelBeforeTemplate)
    {
        $button = Html::a(Yii::t('wk-widget-gridview', 'Customize'), '#',
            [
                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-default wk-btn-customizeDialog',
                'style' => 'text-align: right;',
            ]);

        $panelBeforeTemplate = strtr($panelBeforeTemplate, ['{customizeDialog}' => $button]);
    }

    protected function prepareColumns()
    {
        if ($_COOKIE[$this->config['id']]) {
            $cookieColumns = $this->CookieColumns();
            $this->config['columns'] = array_merge($cookieColumns->hidden, $cookieColumns->visible);
        }
    }

    protected function CookieColumns()
    {
        $visible = [];
        $hidden = [];
        $columns = $this->configColumns;

        $process = [];

        array_map(function ($column) use (&$visible, &$process) {
            if (isset($column['options']['wk-widget'])) {
                $visible[] = $column;
            } else {
                $process[] = $column;
            }

        }, $columns);

        if ($_COOKIE[$this->config['id']]) {
            $cookieOptions = json_decode($_COOKIE[$this->config['id']]);

            if (property_exists($cookieOptions, 'visible')
                && !empty($cookieOptions->visible)
            ) {
                foreach ($cookieOptions->visible as $colCookie) {
                    $filterCol = array_filter($process, function ($col) use ($colCookie) {
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
                    $column['visible'] = false;
                });
            }

        }

        array_map(function ($column) use (&$visible, &$hidden) {
            if (isset($column['visible']) && $column['visible'] === false) {
                $hidden[] = $column;
            } else {
                $visible[] = $column;
            }

        }, $process);


        return (object)[
            'visible' => $visible,
            'hidden' => $hidden,
        ];
    }

    protected function preparePager()
    {
        if ($_COOKIE[$this->config['id']]) {
            $cookieOptions = json_decode($_COOKIE[$this->config['id']]);

            if (property_exists($cookieOptions, 'pager') && $cookieOptions->pager >= 10 && $cookieOptions->pager <= 100) {
                $this->config['dataProvider']->pagination->pageSize = $cookieOptions->pager;
            }

            if (property_exists($cookieOptions, 'sort')) {
                if (substr($cookieOptions->sort, 0, 1) === '-') {
                    $cookieOptions->sort = substr($cookieOptions->sort, 1);
                    $direction = SORT_DESC;
                } else {
                    $direction = SORT_ASC;
                }

                $this->config['dataProvider']->sort->defaultOrder = [$cookieOptions->sort => $direction];
            }
        }
    }

}