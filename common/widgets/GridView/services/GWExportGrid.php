<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.05.2017
 * Time: 14:55
 */

namespace common\widgets\GridView\services;


use common\widgets\ReportLoader\ReportByModel;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class GWExportGrid
{
    private $config;
    /** @var GWFilterDialog */
    private $GWFilterDialog;
    /** @var GWExportGridConfig */
    private $exportGridConfig;
    /** @var  ActiveRecord */
    private $filterModel;
    private $formName;
    /** @var  ActiveDataProvider */
    private $dataProvider;

    public static function lets($config)
    {
        if (!($config['exportGrid'] instanceof GWExportGridConfig)) {
            throw new \Exception('exportGrid must be GWExportGridConfig class');
        }

        if ($config['exportGrid']->enable === false) {
            throw new \Exception('GWExportGridConfig->enable must be true');
        }

        return new self($config);
    }

    public function __construct($config)
    {
        $this->config = $config;
        $this->exportGridConfig = $config['exportGrid'];
        $this->filterModel = $config['filterModel'];
        $this->formName = $this->filterModel->formName();
        $this->dataProvider = $config['dataProvider'];
    }

    public function prepareConfig(array &$jsScripts, &$panelBeforeTemplate)
    {
        $this->prepareJS($jsScripts);
        $this->makeButtonOnToolbar($panelBeforeTemplate);

        return $this->config;
    }

    /**
     * @param GWFilterDialog|null $GWFilterDialog
     */
    public function export(GWFilterDialog $GWFilterDialog = null)
    {
        $this->GWFilterDialog = $GWFilterDialog;
        if (Yii::$app->request->isAjax && Yii::$app->request->post('_report', false)) {
            Yii::$app->response->clearOutputBuffers();
            exit($this->letsExport());
        }
    }

    protected function prepareJS(&$jsScripts)
    {
        $options = [];

        $json_options = json_encode($options, JSON_UNESCAPED_UNICODE);

        $jsScripts[] = "$('#{$this->config['id']}-pjax').wkexport($json_options);";
    }

    protected function makeButtonOnToolbar(&$panelBeforeTemplate)
    {
        $button = Html::a(Yii::t('wk-widget-gridview', 'Export'), '#',
            [
                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger wk-loading wk-btn-exportGrid',
                'wk-loading' => true,
                'style' => 'text-align: right;',
                'data-pjax' => '0',
            ]);
        
        $panelBeforeTemplate = strtr($panelBeforeTemplate, ['{exportGrid}' => $button]);
    }

    protected function filterString()
    {
        $output = $this->getFilterString();
        if ($this->GWFilterDialog instanceof GWFilterDialog) {
            $output .= $this->GWFilterDialog->getAdditionFilterString();
        }

        return $output;
    }

    protected function getReportDisplayName()
    {
        if (isset($this->config['panelHeading']['title'])) {
            return $this->config['panelHeading']['title'];
        }

        return Yii::t('wk-widget-gridview', 'Report');
    }

    protected function letsExport()
    {
        $report = new ReportByModel($this->dataProvider, $this->exportGridConfig->format, $this->getDataColumns());
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
                $value = $this->itemsValueExists($this->filterModel, $attr);
                $output .= $this->filterModel->getAttributeLabel($attr) . ' = "' . $value . '"; ';
            }
        }

        return $output;
    }

    protected function getDataColumns()
    {
        return array_filter($this->config['columns'], function ($column) {
            return $column['class'] === '\kartik\grid\DataColumn' && (!isset($column['visible']) || $column['visible'] === true);
        });
    }

    protected function itemsValueExists(ActiveRecord $model, $attribute)
    {
        if (method_exists($model, 'itemsValues') && $items = $model::itemsValues($attribute)) {
            return $items[$model[$attribute]];
        }

        return $model[$attribute];
    }
}