<?php

namespace common\widgets\GridView;

use common\widgets\GridView\assets\GridViewAsset;
use common\widgets\GridView\assets\TextFieldPropellerAsset;
use common\widgets\GridView\services\GWCustomizeDialog;
use common\widgets\GridView\services\GWExportGrid;
use common\widgets\GridView\services\GWExportGridConfig;
use common\widgets\GridView\services\GWFilterDialog;
use common\widgets\GridView\services\GWFilterDialogConfig;
use common\widgets\GridView\services\GWPrepareColumns;
use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\data\BaseDataProvider;
use yii\db\ActiveQuery;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;
use yii\web\View;

class GridView extends \kartik\grid\GridView
{
    public $crudSettings;
    public $panelHeading;
    public $selectColumn;
    public $serialColumn;
    public $minHeight;
    public $customizeDialog;
    /** @var  GWFilterDialogConfig|null */
    public $filterDialog;
    /** @var  GWExportGridConfig|null */
    public $exportGrid;
    public $jsOptions = [];
    public $js = [];
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
    protected $optionsWidget;
    protected $GWExportGrid;

    public function __construct(array $config = [])
    {
        $this->registerTranslations();
        $config = $this->setDefaults($config);
        $this->optionsWidget = $config;

        if ($this->optionsWidget['customizeDialog'] === true) {
            $this->optionsWidget = GWCustomizeDialog::lets($config)->prepareConfig($this->js, $this->panelBeforeTemplate);
        }

        if ($this->optionsWidget['filterDialog']->enable) {
            $this->optionsWidget = GWFilterDialog::lets($this->optionsWidget)->prepareConfig($this->js, $this->panelBeforeTemplate);
        }

        if ($this->optionsWidget['exportGrid']->enable) {
            $this->GWExportGrid = GWExportGrid::lets($this->optionsWidget);
            $this->optionsWidget = $this->GWExportGrid->prepareConfig($this->js, $this->panelBeforeTemplate);
        }

        $this->templatesPrepare();

        parent::__construct($this->optionsWidget);
    }

    public function init()
    {
        parent::init();
    }

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-widget-gridview'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    /**
     * @return string
     */
    public function run()
    {
        if ($this->filterDialog->enable) {
            $GWFilterDialog = GWFilterDialog::lets($this->optionsWidget);
            $GWFilterDialog->makeFilter($this);
        }

        if ($this->exportGrid->enable) {
            $this->GWExportGrid->export($GWFilterDialog);
        }

        $this->makeGridSelected2StorageJs();
        $this->makeDialogMessagesJs();
        $this->loadDataJs();

        parent::run();
        $this->registerAssetsByWk();
    }

    protected function endPjax()
    {
        if ($this->customizeDialog === true) {
            GWCustomizeDialog::lets($this->optionsWidget)->makeColumnsContent($this->dataProvider, $this->filterModel, $this->id);
        }

        $view = $this->getView();
        $view->registerJs(file_get_contents(Yii::getAlias('@npm') . '/propellerkit/components/textfield/js/textfield.js'));
        $view->registerJs(file_get_contents(Yii::getAlias('@npm') . '/propellerkit/components/checkbox/js/checkbox.js'));

        return parent::endPjax();
    }

    protected function setDefaults($config)
    {
        $config['hover'] = isset($config['hover']) ? $config['hover'] : true;
        $config['pjax'] = isset($config['pjax']) ? $config['pjax'] : true;
        $config['customizeDialog'] = isset($config['customizeDialog']) ? $config['customizeDialog'] : true;
        $config['serialColumn'] = isset($config['serialColumn']) ? $config['serialColumn'] : true;
        $config['selectColumn'] = isset($config['selectColumn']) ? $config['selectColumn'] : true;
        $config['rightBottomToolbar'] = isset($config['rightBottomToolbar']) ? $config['rightBottomToolbar'] : $this->rightBottomToolbar;
        $this->minHeight = isset($config['minHeight']) ? $config['minHeight'] : false;
        $config['id'] = isset($config['id']) ? $config['id'] : $this->getId();

        if (isset($config['minHeight'])) {
            $config['containerOptions'] = array_replace_recursive(
                is_array($config['containerOptions']) ? $config['containerOptions'] : [],
                ['style' => "min-height: {$config['minHeight']}px;"]
            );
        }

        $this->selectColumn = isset($config['selectColumn']) ? $config['selectColumn'] : true;
        //    $config['pjaxSettings']['loadingCssClass'] = isset($config['pjaxSettings']['loadingCssClass']) ? $config['pjaxSettings']['loadingCssClass'] : false;
        $config['resizableColumns'] = isset($config['resizableColumns']) ? $config['resizableColumns'] : false;

        $this->createCrudButtons($config);
        $this->setPanelHeading($config);

        $config['columns'] = GWPrepareColumns::lets($config)->prepare();

        $config['filterDialog'] = $config['filterDialog'] instanceof GWFilterDialogConfig
            ? $config['filterDialog']->build()
            : GWFilterDialogConfig::set()->enable(false)->build();

        $config['exportGrid'] = $config['exportGrid'] instanceof GWExportGridConfig
            ? $config['exportGrid']->build()
            : GWExportGridConfig::set()->enable(false)->build();

        return $config;
    }

    protected function createCrudButtons(&$config)
    {
        $crudSettings = $config['crudSettings'];
        $crudButtons = '';
        if (is_array($crudSettings) && count($crudSettings) > 0) {
            foreach ($crudSettings as $key => $crudUrl) {
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

        unset($config['crudSettings']);
    }

    protected function templatesPrepare()
    {
        $this->panelBeforeTemplate = strtr($this->panelBeforeTemplate, ['{customizeDialog}' => '', '{filterDialog}' => '', '{exportGrid}' => '']);
        $this->panelAfterTemplate = strtr($this->panelAfterTemplate, ['{rightBottomToolbar}' => $this->optionsWidget['rightBottomToolbar']]);
    }

    protected function setPanelHeading(&$config)
    {
        $panelHeading = $config['panelHeading'];

        if (is_array($panelHeading) && count($panelHeading) > 0) {
            $icon = '';
            $title = '';
            foreach ($panelHeading as $key => $option) {
                switch ($key) {
                    case 'icon':
                        $icon = $option . ' ';
                        break;
                    case 'title':
                        $title = $option;
                        break;
                    default:
                        new \Exception(Yii::t('wk-widget-gridview', "In 'setPanelHeading' array must be only this keys ['icon', 'title']. Passed '{key}'", [
                            'key' => $key,
                        ]));
                }
            }
            $config['panel']['heading'] = isset($config['panel']['heading']) ? $config['panel']['heading'] : '<h3 class="panel-title">' . $icon . $title . '</h3>';
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

    protected function makeGridSelected2StorageJs()
    {
        $options = [
            'storage' => 'selectedRows',
            'selectedPanelClass' => 'selectedPanel',
            'recordsSelectedMessage' => Yii::t('wk-widget-gridview', 'Records selected <b>{from}</b> from <b>{all}</b>'),
        ];

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);

        $this->js[] = "$('#{$this->id}-pjax').gridselected2storage($options);";
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
}