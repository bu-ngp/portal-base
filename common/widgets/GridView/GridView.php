<?php

namespace common\widgets\GridView;

use common\widgets\GridView\assets\GridViewAsset;
use common\widgets\GridView\services\GWAddCrudConfigForCreate;
use common\widgets\GridView\services\GWAddCrudConfigForUpdate;
use common\widgets\GridView\services\GWCustomizeDialog;
use common\widgets\GridView\services\GWDeleteCrudConfig;
use common\widgets\GridView\services\GWDeleteCrudConfigForCreate;
use common\widgets\GridView\services\GWExportGrid;
use common\widgets\GridView\services\GWExportGridConfig;
use common\widgets\GridView\services\GWFilterDialog;
use common\widgets\GridView\services\GWFilterDialogConfig;
use common\widgets\GridView\services\GWPrepareColumns;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class GridView extends \kartik\grid\GridView
{
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
    /** @var  GWFilterDialogConfig|null */
    public $filterDialog;
    /** @var  GWExportGridConfig|null */
    public $exportGrid;
    public $toolbar = [];
    public $leftBottomToolbar = '';
    public $rightBottomToolbar = '';
    public $pjaxSettings = [
        'loadingCssClass' => 'wk-widget-grid-loading',
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
        if ($this->filterDialog->enable) {
            $filterString = GWFilterDialog::lets($this)->prepareConfig()->makeFilter();
        }

        if ($this->exportGrid->enable) {
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

        $this->filterDialog = empty($this->filterDialog) ? GWFilterDialogConfig::set()->enable(false)->build() : $this->filterDialog->build();
        $this->exportGrid = empty($this->exportGrid) ? GWExportGridConfig::set()->enable(false)->build() : $this->exportGrid->build();
    }

    protected function createCrudButtons()
    {
        $crudButtons = '';
        $actionButtons = [];

        $this->selectGridState($actionButtons);

        if (is_array($this->crudSettings) && count($this->crudSettings) > 0) {
            foreach ($this->crudSettings as $key => $crudUrl) {
                // $crudUrl = is_array($crudUrl) ? Url::to($crudUrl) : $crudUrl;

                switch ($key) {
                    case 'create':
                        $options = [
                            'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success wk-gridview-crud-create',
                            'data-pjax' => '0'
                        ];

                        $isTypeObject = isset($crudUrl['class']);

                        if ($isTypeObject) {
                            $crudUrl = Yii::createObject([
                                'class' => $crudUrl['class'],
                                'urlGrid' => $crudUrl['urlGrid'],
                                'inputName' => $crudUrl['inputName'],
                            ]);

                            $GWCreateCrud = $crudUrl->build();
                            $crudUrl = is_array($GWCreateCrud->urlGrid) ? Url::to($GWCreateCrud->urlGrid) : $GWCreateCrud->urlGrid;

                            $options = array_merge($options, [
                                'input-name' => $GWCreateCrud->inputName,
                            ]);

                            $this->addCrudCreateSelectedToQuery();
                        }


                        /*  if ($crudUrl instanceof GWAddCrudConfigForCreate) {

                          }*/


//                        if ($crudUrl instanceof GWAddCrudConfigForUpdate) {
//                            $GWCreateCrud = $crudUrl->build();
//                            $crudUrl = '#';
//
//                            $options = array_merge($options, [
//                                'url-action' => is_array($GWCreateCrud->urlAction) ? Url::to($GWCreateCrud->urlAction) : $GWCreateCrud->urlAction,
//                                'url-grid' => is_array($GWCreateCrud->urlGrid) ? Url::to($GWCreateCrud->urlGrid) : $GWCreateCrud->urlGrid,
//                                'wk-exclude' => $GWCreateCrud->excludeFromId,
//                            ]);
//
//                        }

                        $crudButtons .= Html::a(/*'<i class="fa fa-2x fa-plus-square-o"></i> ' .*/
                            Yii::t('wk-widget-gridview', 'Create'), $crudUrl, $options);
                        break;
                    case 'update':
//                        $actionButtons['choose'] = function ($url, $model) use ($crudUrl) {
//                            $customurl = Url::to([$crudUrl, 'id' => $model->primaryKey]);
//                            return Html::a('<i class="fa fa-2x fa-check-square-o"></i>', $customurl, ['title' => 'Выбрать', 'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0']);
//                        };
                        $actionButtons['update'] = function ($url, $model) use ($crudUrl) {
                            $customurl = Url::to([$crudUrl, 'id' => $model->primaryKey]);
                            return Html::a('<i class="fa fa-2x fa-pencil-square-o"></i>', $customurl, ['title' => 'Обновить', 'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-primary', 'data-pjax' => '0']);
                        };


//                        $crudButtons .= Html::a(Yii::t('wk-widget-gridview', 'Update'), $crudUrl,
//                            [
//                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary wk-btn-update',
//                                'data-pjax' => '0'
//                            ]);
                        break;
                    case 'delete':

                        $options = [
                            'title' => 'Удалить',
                            'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-gridview-crud-delete',
                            'data-pjax' => '0'
                        ];

                        $isTypeObject = isset($crudUrl['class']);

                        if ($isTypeObject) {
                            $crudUrl = Yii::createObject([
                                'class' => $crudUrl['class'],
                                'urlGrid' => $crudUrl['urlGrid'],
                                'inputName' => $crudUrl['inputName'],
                            ]);

                            $GWDeleteCrud = $crudUrl->build();
                            $crudUrl = '#';

                            $options = array_merge($options, [
                                'input-name' => $GWDeleteCrud->inputName,
                            ]);

                            $actionButtons['delete'] = function ($url, $model) use ($options) {
                                $options = array_merge($options, [
                                    'wk-id' => $model->primaryKey,
                                ]);

                                return Html::a('<i class="fa fa-2x fa-trash-o"></i>', '#', $options);
                            };
                        } else {
                            if (!is_array($crudUrl)) {
                                $crudUrl = [$crudUrl];
                            }

                            $actionButtons['delete'] = function ($url, $model) use ($crudUrl, $options) {
                                $crudUrl['id'] = $model->primaryKey;
                                $crudUrl['mainId'] = Yii::$app->request->get('id');
                                $customurl = Url::to($crudUrl);

                                return Html::a('<i class="fa fa-2x fa-trash-o"></i>', $customurl, $options);
                            };
                        }


//
//                        $crudButtons .= Html::a(Yii::t('wk-widget-gridview', 'Delete'), $crudUrl, $options);
                        break;
                    default:
                        new \Exception(Yii::t('wk-widget-gridview', "In 'crudOptions' array must be only this keys ['create', 'update', 'delete']. Passed '{key}'", [
                            'key' => $key,
                        ]));
                }
            }
        }

        if (count($actionButtons) > 0) {
            $tmpl = '{' . implode("} {", array_keys($actionButtons)) . '}';

            array_unshift($this->columns, [
                'class' => 'kartik\grid\ActionColumn',
                'header' => Html::encode('Действия'),
                'contentOptions' => ['class' => 'wk-grid-action-buttons'],
                'buttons' => $actionButtons,
                'template' => $tmpl,
                'options' => ['wk-widget' => true],
            ]);
        }

        $this->panelBeforeTemplate = strtr($this->panelBeforeTemplate, ['{crudToolbar}' => $crudButtons]);
        $this->panelAfterTemplate = strtr($this->panelAfterTemplate, ['{crudButtons}' => $crudButtons]);
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
                $("#$id").yiiGridView('applyFilter');
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

    protected function addCrudCreateSelectedToQuery()
    {
        if ($this->dataProvider instanceof ActiveDataProvider) {
            $condition = '';
            if ($_oper = Yii::$app->request->headers['wk-grid-oper']) {
                $condition = $_oper === 'add' ? '1=2' : '';
            }

            if (Yii::$app->request->headers['wk-choose']) {
                if ($_choose = json_decode(Yii::$app->request->headers['wk-choose'])) {
                    if (is_array($_choose)) {
                        $condition = ['in', 'name', $_choose];
                    }
                }
            }

            if (!empty($condition)) {
                $this->dataProvider->query->andWhere($condition);
            }

        }
    }

    protected function selectGridState(array &$actionButtons)
    {
        if ($this->gridExcludeIdsFunc instanceof \Closure
            && Yii::$app->request->headers['wk-selected']
            && ($_selected = json_decode(Yii::$app->request->headers['wk-selected']))
            && (property_exists($_selected, 'exclude') || property_exists($_selected, 'reject'))
        ) {
            $actionButtons['choose'] = function ($url, $model) use ($_selected) {
                $url = $_selected->url . (preg_match('/\?/', $_selected->url) ? '&' : '?') . 'grid=' . urlencode($_selected->gridID) . '&selected=' . urlencode($model->primaryKey);

                return Html::a('<i class="fa fa-2x fa-check-square-o"></i>', $url, ['title' => 'Выбрать', 'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0']);
            };

            $func = $this->gridExcludeIdsFunc;

            $func($this->dataProvider->query, property_exists($_selected, 'exclude') ? $_selected->exclude : [$_selected->reject]);
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
            $gridInject = Yii::createObject($this->gridInject['class'], [[
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