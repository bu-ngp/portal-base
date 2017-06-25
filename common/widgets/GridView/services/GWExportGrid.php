<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.05.2017
 * Time: 14:55
 */

namespace common\widgets\GridView\services;


use common\widgets\GridView\GridView;
use common\widgets\ReportLoader\ReportByModel;
use kartik\grid\DataColumn;
use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

class GWExportGrid
{
    /** @var GridView */
    private $gridView;
    private $formName;
    private $additionFilterString = '';

    public static function lets(GridView $gridView)
    {
        if (!($gridView->exportGrid instanceof GWExportGridConfig)) {
            throw new \Exception('exportGrid must be GWExportGridConfig class');
        }

        if ($gridView->exportGrid->enable === false) {
            throw new \Exception('GWExportGridConfig->enable must be true');
        }

        return new self($gridView);
    }

    public function __construct(GridView $gridView)
    {
        $this->gridView = $gridView;
        $this->formName = $gridView->filterModel->formName();
    }

    public function prepareConfig($additionFilterString = '')
    {
        $this->prepareJS();
        $this->makeButtonOnToolbar();
        $this->additionFilterString = $additionFilterString;

        return $this;
    }

    public function export()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->post('_report', false)) {
            $format = Yii::$app->request->post('type', 'pdf');

            Yii::$app->response->clearOutputBuffers();
            exit($this->letsExport($format));
        }
    }

    protected function prepareJS()
    {
        $this->gridView->registerJs("$('#{$this->gridView->id}-pjax').wkexport();");
    }

    protected function makeButtonOnToolbar()
    {
        if (!$this->gridView->exportGrid->format) {
            $this->gridView->exportGrid->format[] = GridView::PDF;
        }

        if (count($this->gridView->customButtons) > 0) {
            $this->gridView->customButtons[] = '{divider}';
        }

        foreach ($this->gridView->exportGrid->format as $format) {
            $properties = $this->buttonProperties($format);

            $button = Html::a($properties['description'] . ' <i class="fa ' . $properties['icon'] . '"></i>', '#',
                [
                    'class' => "btn btn-xs pmd-btn-flat pmd-ripple-effect {$properties['class']} wk-loading wk-btn-exportGrid",
                    'wk-export' => $format,
                    'wk-loading' => true,
                    'data-pjax' => '0',
                ]);

            $this->gridView->customButtons[] = $button;
        }
    }

    protected function buttonProperties($format)
    {
        switch ($format) {
            case GridView::EXCEL:
                return [
                    'class' => 'btn-success',
                    'icon' => 'fa-file-excel-o',
                    'description' => Yii::t('wk-widget-gridview', 'Export to Excel'),
                ];
            default:
                return [
                    'class' => 'btn-danger',
                    'icon' => 'fa-file-pdf-o',
                    'description' => Yii::t('wk-widget-gridview', 'Export to PDF'),
                ];
        }
    }

    protected function filterString()
    {
        $filter = $this->getFilterString() . $this->additionFilterString;
        return empty($filter) ? '' : Yii::t('wk-widget-gridview', 'Add. filter: ') . $filter;
    }

    protected function getReportDisplayName()
    {
        return ArrayHelper::getValue($this->gridView->panelHeading, 'title', Yii::t('wk-widget-gridview', 'Report'));
    }

    protected function letsExport($format = 'pdf')
    {
        $report = new ReportByModel($this->gridView->dataProvider, $format, $this->getDataColumns());
        $report->setFilterString($this->filterString());
        $report->reportDisplayName = $this->getReportDisplayName();
        $report->reportid = $this->formName;
        return $report->report();
    }

    protected function getFilterString()
    {
        $output = '';
        if ($filter = Yii::$app->request->get($this->formName, false)) {
            foreach ($filter as $attr => $value) {
                $value = $this->itemsValueExists($this->gridView->filterModel, $attr);
                $output .= $this->gridView->filterModel->getAttributeLabel($attr) . ' = "' . $value . '"; ';
            }
        }

        return $output;
    }

    protected function getDataColumns()
    {
        return array_filter($this->gridView->columns, function ($column) {
            return $column instanceof DataColumn && $column->visible;
        });
    }

    protected function itemsValueExists(Model $model, $attribute)
    {
        if (method_exists($model, 'itemsValues') && $items = $model::itemsValues($attribute)) {
            return $items[$model[$attribute]];
        }

        return $model[$attribute];
    }
}