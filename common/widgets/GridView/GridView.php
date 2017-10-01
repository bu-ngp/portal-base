<?php

namespace common\widgets\GridView;

use common\widgets\GridView\assets\GridViewAsset;
use common\widgets\GridView\services\ActionButtons;
use common\widgets\GridView\services\GWAddCrudConfigForCreate;
use common\widgets\GridView\services\GWCustomizeDialog;
use common\widgets\GridView\services\GWExportGrid;
use common\widgets\GridView\services\GWExportGridConfiguration;
use common\widgets\GridView\services\GWFilterDialog;
use common\widgets\GridView\services\GWFilterDialogConfiguration;
use common\widgets\GridView\services\GWPrepareColumns;
use Yii;
use yii\bootstrap\Html;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class GridView extends \kartik\grid\GridView
{
    const ADD = 'add';
    const EDIT = 'edit';

    public $hover = true;
    public $pjax = true;
    public $resizableColumns = false;
    public $pager = [
        'prevPageLabel' => '<i class="fa fa-angle-left"></i>',
        'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
    ];

    public $crudSettings = [];
    public $panelHeading = [];
    public $selectColumn = false;
    public $serialColumn = true;
    public $minHeight = false;
    public $customizeDialog = true;
    /** @var  GWFilterDialogConfiguration|array */
    public $filterDialog = [
        'enable' => false,
    ];
    /** @var  GWExportGridConfiguration|array */
    public $exportGrid = [
        'enable' => false,
    ];
    public $toolbar = [];
    public $leftBottomToolbar = '';
    public $rightBottomToolbar = '';
    public $pjaxSettings = [
        'loadingCssClass' => 'wk-widget-grid-loading',
      //  'loadingCssClass' => false,
       // 'options' => ['clientOptions' => ['async' => false]],
    ];
    public $panelAfterTemplate = <<< HTML
        <div class="btn-toolbar kv-grid-toolbar" role="toolbar" style="display: inline-block;">
            <div class="btn-group">
                {leftBottomToolbar}
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
        <div class="wk-grid-errors">
            {gridErrors}
        </div>
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
                <div class="btn-group wk-custom-buttons">
                    {customButtons}
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
    public $customButtons = [];
    public $gridExcludeIdsFunc;
    public $gridInject;
    protected $js = [];
    /** @var  GWCustomizeDialog */
    protected $GWCustomizeDialog;
    /** @var GWFilterDialog */
    protected $GWFilterDialog;
    /** @var GWExportGrid */
    protected $GWExportGrid;
    /** @var  GWAddCrudConfigForCreate */
    protected $GWCreateCrud;

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

        parent::init();
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

        if ($this->filterDialog->isEnable()) {
            $filterString = GWFilterDialog::lets($this)->prepareConfig()->makeFilter();
        }

        if ($this->exportGrid->isEnable()) {
            GWExportGrid::lets($this)->prepareConfig($filterString)->export();
        }

        $this->selectedAttribute();
        $this->wkidAttribute();
        $this->panelBeforeTemplate = strtr($this->panelBeforeTemplate, [
            '{gridErrors}' => ($gridErrors = $this->saveSelectedModel()) ? Html::errorSummary($gridErrors) : '',
        ]);
        $this->makeCustomButtons();
        $this->templatesPrepare();
        $this->initGridJs();
        $this->makeDialogMessagesJs();
        $this->loadPropellerJS();
        $this->loadDataJs();

        parent::run();
        $this->registerAssetsByWk();
    }

    protected function initLayout()
    {
        parent::initLayout();
        $this->layout = strtr($this->layout, ['{items}' => '{items}<div class="wk-widget-grid-loading-container"></div>']);
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

        if (!(Yii::$app->request->isAjax && (Yii::$app->request->get('_pjax') || Yii::$app->request->post('_report', false)))) {
            $this->dataProvider = new ArrayDataProvider();
        }

        $this->createCrudButtons();
        $this->setPanelHeading();

        GWPrepareColumns::lets($this)->prepare();

        $this->filterDialog = Yii::createObject('common\widgets\GridView\services\GWFilterDialogConfiguration', [[
            'enable' => $this->filterDialog['enable'],
            'filterModel' => $this->filterDialog['filterModel'],
            'filterView' => $this->filterDialog['filterView'],
        ]]);

        $this->exportGrid = Yii::createObject('common\widgets\GridView\services\GWExportGridConfiguration', [[
            'enable' => $this->exportGrid['enable'],
            'format' => $this->exportGrid['format'],
            'idReportLoader' => $this->exportGrid['idReportLoader'],
        ]]);
    }

    protected function createCrudButtons()
    {
        $actionButtons = new ActionButtons($this);

        if ($actionButtons->exists()) {
            array_unshift($this->columns, [
                'class' => 'kartik\grid\ActionColumn',
                'header' => Html::encode('Действия'),
                'contentOptions' => ['class' => 'wk-grid-action-buttons'],
                'buttons' => $actionButtons->getButtons(),
                'template' => $actionButtons->template(),
                'options' => ['wk-widget' => true],
            ]);
        }

        $this->panelBeforeTemplate = strtr($this->panelBeforeTemplate, ['{crudToolbar}' => $actionButtons->getCreateButton()]);
    }

    protected function templatesPrepare()
    {
        $this->panelBeforeTemplate = strtr($this->panelBeforeTemplate, ['{customButtons}' => '']);
        $this->panelAfterTemplate = strtr($this->panelAfterTemplate, ['{leftBottomToolbar}' => $this->leftBottomToolbar]);
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
                $("#$id").yiiGridView({"filterUrl": window.location.search}); /* сокращает url purifyingUrl() */
                
                function Func_$id() {
                    var busy = false;
                    $.each($("div[data-pjax-container]"), function() {
                        if ($(this)[0].busy) {
                            busy = $(this)[0].busy;
                        }
                    });
                
                    if (!busy) {
                        $("#$id").yiiGridView('applyFilter');
                    } else {
                        setTimeout(function() {
                            Func_$id();
                        }, 300);
                    } 
                }
                
                Func_$id();               
            }
EOT;
    }

    protected function initGridJs()
    {
        $options = [
            'messages' => [
                'titleCrudCreateDialogMessage' => Yii::t('wk-widget-gridview', 'Choose rows'),
                'applyButtonMessage' => Yii::t('wk-widget-gridview', 'Apply'),
                'closeButtonMessage' => Yii::t('wk-widget-gridview', 'Close'),
                'redirectToGridButtonCrudCreateDialogMessage' => Yii::t('wk-widget-gridview', 'Follow to Grid Page'),
                'removeRecordConfirm' => Yii::t('wk-widget-gridview', 'Remove record. Are you sure?'),
            ],
        ];

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);

        $this->js[] = "$('#{$this->id}-pjax').wkgridview($options);";
    }

    protected function loadPropellerJS()
    {
        $scripts = [
            Yii::getAlias('@npm') . '/propellerkit/components/textfield/js/textfield.js',
            Yii::getAlias('@npm') . '/propellerkit/components/checkbox/js/checkbox.js',
            //  Yii::getAlias('@npm') . '/propellerkit/components/button/js/ripple-effect.js',
        ];

        foreach ($scripts as $script) {
            if (file_exists($script)) {
                $this->js[] = file_get_contents($script);
            }
        }
    }

    protected function makeCustomButtons()
    {
        if ($this->customButtons) {
            $buttons = '';
            array_map(function ($liContent) use (&$buttons) {
                if ($liContent === '{divider}') {
                    $buttons .= '<li role="separator" class="divider"></li>';
                } else {
                    $buttons .= "<li>$liContent</li>";
                }
            }, $this->customButtons);

            $this->panelBeforeTemplate = strtr($this->panelBeforeTemplate, ['{customButtons}' => <<<EOT
                <div class="btn-group wk-widget-grid-custom-button">
                    <button type="button" class="btn pmd-btn-flat pmd-ripple-effect btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="glyphicon glyphicon-option-vertical"></i></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        $buttons
                    </ul>
                </div>
EOT
            ]);
        }
    }

    protected function selectedAttribute()
    {
        if (Yii::$app->request->get('grid') === $this->id) {
            $this->options['wk-selected'] = Yii::$app->request->get('selected');
        }
    }

    protected function wkidAttribute()
    {
        if (Yii::$app->request->get('id')) {
            $this->options['wk-id'] = Yii::$app->request->get('id');
        }
    }

    protected function saveSelectedModel()
    {
        if ($this->gridInject
            && Yii::$app->request->isAjax
            && Yii::$app->request->get('_pjax')
            && Yii::$app->request->get('grid')
            && Yii::$app->request->get('selected')
            && ($_oper = Yii::$app->request->headers['wk-grid-oper'])
            && ($_oper === 'save')
        ) {
            if (empty($this->gridInject['saveFunc'])) {
                $this->gridInject['saveFunc'] = function (\yii\db\ActiveRecord $model, $mainId, $mainField, $foreignField, $foreignId) {
                    $model->$mainField = $mainId;
                    $model->$foreignField = $foreignId;
                    $model->save();
                };
            }

            $gridInject = Yii::createObject('common\widgets\GridView\services\GWSaveModelForUpdate', [[
                'modelClassName' => $this->gridInject['modelClassName'],
                'mainField' => $this->gridInject['mainField'],
                'foreignField' => $this->gridInject['foreignField'],
                'saveFunc' => $this->gridInject['saveFunc'],
                'mainIdParameterName' => $this->gridInject['mainIdParameterName'],
            ]]);

            return $gridInject->save();
        }

        return [];
    }
}