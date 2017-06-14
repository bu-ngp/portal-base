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
use yii\web\Response;
use yii\web\View;

class GWExportGrid
{
    private $config;
    /** @var GWFilterDialog */
    private $GWFilterDialog;

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

    private function letsExport()
    {
        $report = new ReportByModel($this->config['dataProvider']);
        if ($this->GWFilterDialog instanceof GWFilterDialog) {
            $report->setAdditionalFilterString($this->GWFilterDialog->getAdditionFilterString());
        }
        $report->reportDisplayName = isset($this->config['panelHeading']['title']) ? $this->config['panelHeading']['title'] : Yii::t('wk-widget-gridview', 'Report');
        $report->reportid = $this->config['filterModel']->formName();
        $report->reportType = ReportByModel::EXCEL;
        return $report->report();
    }
}