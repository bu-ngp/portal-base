<?php

namespace common\widgets\GridView;

use common\widgets\GridView\assets\GridViewAsset;
use common\widgets\GridView\services\GWCustomizeDialog;
use common\widgets\GridView\services\GWExportGrid;
use common\widgets\GridView\services\GWExportGridConfig;
use common\widgets\GridView\services\GWFilterDialog;
use common\widgets\GridView\services\GWFilterDialogConfig;
use common\widgets\GridView\services\GWPrepareColumns;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class GridView extends \kartik\grid\GridView
{
    public $hover = true;
    public $pjax = true;
    public $resizableColumns = false;

    public $crudSettings = [];
    public $panelHeading = [];
    public $selectColumn = true;
    public $serialColumn = true;
    public $minHeight = false;
    public $customizeDialog = true;
    /** @var  GWFilterDialogConfig|null */
    public $filterDialog;
    /** @var  GWExportGridConfig|null */
    public $exportGrid;
    public $toolbar = [];
    public $rightBottomToolbar = '';
    public $panelAfterTemplate = <<< HTML
        <div class="btn-toolbar kv-grid-toolbar" role="toolbar" style="display: inline-block;">
            <div class="btn-group">
                {crudButtons}
            </div>
        </div>
        {after}
        <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
            <div class="btn-group">
                {rightBottomToolbar}
            </div>
        </div>
HTML;
    public $panelBeforeTemplate = <<< HTML
        <div>
            <div class="btn-toolbar pull-left kv-grid-toolbar wk-grid-toolbar" role="toolbar">
                <div class="btn-group">
                    {crudToolbar}
                </div>
            </div>
            <div class="btn-toolbar pull-left kv-grid-toolbar" role="toolbar">
                {toolbar}
            </div>
            <div class="btn-toolbar pull-right kv-grid-toolbar" role="toolbar">
                <div class="btn-group-vertical btn-group-xs wk-custom-buttons">
                    {customizeDialog}
                    {filterDialog}
                    {exportGrid}
                </div>
            </div>
        </div>
        {before}
        <div class="clearfix"></div>
HTML;
    public $panelFooterTemplate = <<< HTML
        <div class="kv-panel-pager pull-left">
            {pager}
        </div>
        <div class="selectedPanel pull-right" style="display: none;"></div>
        {footer}
        <div class="clearfix"></div>
HTML;
    protected $js = [];
    /** @var  GWCustomizeDialog */
    protected $GWCustomizeDialog;
    /** @var GWFilterDialog */
    protected $GWFilterDialog;
    /** @var GWExportGrid */
    protected $GWExportGrid;

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-widget-gridview'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    public function init()
    {
        $this->registerTranslations();
        $this->setDefaults();

        return parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        if ($this->customizeDialog) {
            GWCustomizeDialog::lets($this)->prepareConfig()->makeColumnsContent();
        }

        $filterString = '';
        if ($this->filterDialog->enable) {
            $filterString = GWFilterDialog::lets($this)->prepareConfig()->makeFilter();
        }

        if ($this->exportGrid->enable) {
            GWExportGrid::lets($this)->prepareConfig($filterString)->export();
        }

        $this->templatesPrepare();
        $this->initGridJs();
        $this->makeDialogMessagesJs();
        $this->loadPropellerJS();
        $this->loadDataJs();

        parent::run();
        $this->registerAssetsByWk();
    }

    public function registerJs($script)
    {
        $this->js[] = $script;
    }

    protected function setDefaults()
    {
        if ($this->minHeight) {
            $this->containerOptions['style'] = "min-height: {$this->minHeight}px;";
        }

        //  $this->pjaxSettings = ArrayHelper::getValue($this->pjaxSettings, 'loadingCssClass', 'wk-widget-grid-loading');

        $this->createCrudButtons();
        $this->setPanelHeading();

        GWPrepareColumns::lets($this)->prepare();

        $GWFilterDialogConfig = ArrayHelper::getValue($this, 'filterDialog', GWFilterDialogConfig::set()->enable(false));
        $this->filterDialog = $GWFilterDialogConfig->build();

        $GWExportGridConfig = ArrayHelper::getValue($this, 'exportGrid', GWExportGridConfig::set()->enable(false));
        $this->exportGrid = $GWExportGridConfig->build();
    }

    protected function createCrudButtons()
    {
        $crudButtons = '';
        if (is_array($this->crudSettings) && count($this->crudSettings) > 0) {
            foreach ($this->crudSettings as $key => $crudUrl) {
                $crudUrl = is_array($crudUrl) ? Url::to($crudUrl) : $crudUrl;

                switch ($key) {
                    case 'create':
                        $crudButtons .= Html::a(Yii::t('wk-widget-gridview', 'Create'), $crudUrl,
                            [
                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success',
                                'data-pjax' => '0'
                            ]);
                        break;
                    case 'update':
                        $crudButtons .= Html::a(Yii::t('wk-widget-gridview', 'Update'), $crudUrl,
                            [
                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary wk-btn-update',
                                'data-pjax' => '0'
                            ]);
                        break;
                    case 'delete':
                        $crudButtons .= Html::a(Yii::t('wk-widget-gridview', 'Delete'), $crudUrl,
                            [
                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger',
                                'data-pjax' => '0'
                            ]);
                        break;
                    default:
                        new \Exception(Yii::t('wk-widget-gridview', "In 'crudOptions' array must be only this keys ['create', 'update', 'delete']. Passed '{key}'", [
                            'key' => $key,
                        ]));
                }
            }
        }

        $this->panelBeforeTemplate = strtr($this->panelBeforeTemplate, ['{crudToolbar}' => $crudButtons]);
        $this->panelAfterTemplate = strtr($this->panelAfterTemplate, ['{crudButtons}' => $crudButtons]);
    }

    protected function templatesPrepare()
    {
        $this->panelBeforeTemplate = strtr($this->panelBeforeTemplate, ['{customizeDialog}' => '', '{filterDialog}' => '', '{exportGrid}' => '']);
        $this->panelAfterTemplate = strtr($this->panelAfterTemplate, ['{rightBottomToolbar}' => $this->rightBottomToolbar]);
    }

    protected function setPanelHeading()
    {
        if (is_array($this->panelHeading) && count($this->panelHeading) > 0) {
            $icon = ArrayHelper::getValue($this->panelHeading, 'icon', '');
            $title = ArrayHelper::getValue($this->panelHeading, 'title', '');
            $this->panel['heading'] = ArrayHelper::getValue($this->panel, 'heading', '<h3 class="panel-title">' . $icon . ' ' . $title . '</h3>');
        }
    }

    protected function registerAssetsByWk()
    {
        $view = $this->getView();
        GridViewAsset::register($view);

        foreach ($this->js as $script) {
            $view->registerJs($script);
        }
    }

    protected function makeDialogMessagesJs()
    {
        $options = [
            'messages' => [
                'dialogConfirmTitle' => Yii::t('wk-widget-gridview', 'Confirm'),
                'dialogAlertTitle' => Yii::t('wk-widget-gridview', 'Information'),
                'dialogConfirmButtonClose' => Yii::t('wk-widget-gridview', 'No'),
                'dialogConfirmButtonOK' => Yii::t('wk-widget-gridview', 'Yes'),
                'dialogAlertButtonClose' => Yii::t('wk-widget-gridview', 'Close'),
            ],
        ];

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);

        $this->js[] = "wkwidget.init($options);";
    }

    protected function loadDataJs()
    {
        $id = $this->id;
        $this->js[] = <<<EOT
            if ($("#$id").length) {
                $("#$id").yiiGridView({"filterUrl": window.location.search});
                $("#$id").yiiGridView('applyFilter');
            }
EOT;
    }

    protected function initGridJs()
    {
        $this->js[] = "$('#{$this->id}-pjax').wkgridview();";
    }

    protected function loadPropellerJS()
    {
        $textfieldJsPath = Yii::getAlias('@npm') . '/propellerkit/components/textfield/js/textfield.js';
        if (file_exists($textfieldJsPath)) {
            $this->js[] = file_get_contents($textfieldJsPath);
        }

        $checkboxJsPath = Yii::getAlias('@npm') . '/propellerkit/components/checkbox/js/checkbox.js';
        if (file_exists($checkboxJsPath)) {
            $this->js[] = file_get_contents($checkboxJsPath);
        }
    }
}