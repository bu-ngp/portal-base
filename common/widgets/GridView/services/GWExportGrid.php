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
use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\Response;
use yii\web\View;

class GWExportGrid
{
    private $config;
    /** @var GWFilterDialog */
    private $GWFilterDialog;
    /** @var GWExportGridConfig */
    private $exportGridConfig;

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
    }

    public function prepareConfig(array &$jsScripts)
    {
        $this->prepareJS($jsScripts);
        $this->makeButtonOnToolbar();

        return $this->config;
    }

    /**
     * @param GWFilterDialog|null $GWFilterDialog
     */
    public function export(GWFilterDialog $GWFilterDialog = null)
    {
        $this->GWFilterDialog = $GWFilterDialog;
        if (Yii::$app->request->isAjax && $_POST['_report']) {
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

    protected function makeButtonOnToolbar()
    {
        $toolbar = Html::a(Yii::t('wk-widget-gridview', 'Export'), '#',
            [
                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger wk-loading wk-btn-exportGrid',
                'wk-loading' => true,
                'style' => 'text-align: right;',
                'data-pjax' => '0',
            ]);

        $this->config['toolbar'][1]['content'] .= $toolbar;
    }

    protected function letsExport()
    {
        $report = new ReportByModel($this->config['dataProvider']);
        if ($this->GWFilterDialog instanceof GWFilterDialog) {
            $report->setAdditionalFilterString($this->GWFilterDialog->getAdditionFilterString());
        }
        $report->setFilterString($this->getFilterString());
        $report->reportDisplayName = isset($this->config['panelHeading']['title']) ? $this->config['panelHeading']['title'] : Yii::t('wk-widget-gridview', 'Report');
        $report->reportid = $this->config['filterModel']->formName();
        $report->reportType = ReportByModel::EXCEL;
        $report->columnsFromGrid($this->getDataColumns());
        return $report->report();
    }

    protected function getFilterString()
    {
        $output = '';
        if ($filter = $_GET[$this->config['filterModel']->formName()]) {
            foreach ($filter as $attr => $value) {
                $value = $this->itemsValueExists($this->config['filterModel'], $attr);
                $output .= $this->config['filterModel']->getAttributeLabel($attr) . ' = "' . $value . '"; ';
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