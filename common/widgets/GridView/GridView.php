<?php

namespace common\widgets\GridView;

use common\widgets\GridView\services\ActionButtons;
use common\widgets\GridView\services\GWAddCrudConfigForCreate;
use common\widgets\GridView\services\GWCustomizeDialog;
use common\widgets\GridView\services\GWExportGrid;
use common\widgets\GridView\services\GWExportGridConfiguration;
use common\widgets\GridView\services\GWFilterDialog;
use common\widgets\GridView\services\GWFilterDialogConfiguration;
use common\widgets\GridView\services\GWPrepareColumns;
use common\widgets\PropellerAssets\PropellerAsset;
use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Виджет грида с дополнительными возможностями.
 *
 * Грид содержит следующие возможности:
 * * Имеется возможность настроить грид в модальном окне: Изменить видимость, порядок колонок. Количество отображаемых записей на странице грида;
 * * Экспорт данных грида в `PDF` или `Excel`;
 * * Возможность выбора записей из других гридов справочников, при создании или обновлении записи;
 * * Возможность исключить уже выбранные записи из грида
 *
 * ```php
 *     <?= GridView::widget([
 *         'dataProvider' => $dataProvider,
 *         'filterModel' => $searchModel,
 *         'exportGrid' => [
 *             'idReportLoader' => 'wk-Report-Loader',
 *         ],
 *         'columns' => [
 *             'build_name',
 *         ],
 *         'crudSettings' => [
 *             'create' => [
 *                 'urlGrid' => 'build/create',
 *                 'beforeRender' => function () {
 *                     return Yii::$app->user->can(RbacHelper::BUILD_EDIT);
 *                 },
 *             ],
 *             'update' => [
 *                 'url' => 'build/update',
 *                 'beforeRender' => function () {
 *                     return Yii::$app->user->can(RbacHelper::BUILD_EDIT);
 *                 },
 *             ],
 *             'delete' => [
 *                 'url' => 'build/delete',
 *                 'beforeRender' => function () {
 *                     return Yii::$app->user->can(RbacHelper::BUILD_EDIT);
 *                 },
 *             ],
 *         ],
 *         'gridExcludeIdsFunc' => function (ActiveQuery $activeQuery, array $ids) {
 *             $activeQuery->andWhere(['not in', 'build_id', $ids]);
 *         }
 *     ]); ?>
 * ```
 *
 */
class GridView extends \kartik\grid\GridView
{
    /** Вывод грида для добавления записи */
    const ADD = 'add';
    /** Вывод грида для обновления записи */
    const EDIT = 'edit';

    /**
     * @var bool Активировать Hover эффект при наведении мыши на зиписи грида.
     */
    public $hover = true;
    /**
     * @var bool Использовать `pjax` для фильтрации и сортировки записей.
     */
    public $pjax = true;
    /**
     * @var bool Изменение размера колонок грида.
     */
    public $resizableColumns = false;
    /**
     * @var array Конфигурация кнопок пагинации
     */
    public $pager = [
        'prevPageLabel' => '<i class="fa fa-angle-left"></i>',
        'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
    ];

    /**
     * @var array Массив с конфигурацией `CRUD` грида.
     *
     * Содержит следующие опции ключей массива:
     * * `create` - Кнопка добавления записи грида;
     * * `update` - Кнопка обновления записи грида;
     * * `delete` - Кнопка удаления записи грида.
     * Каждое значение может содержать url в виде строки или массива, или конфигурационный массив с определенным набором ключей
     *
     * Конфигурация для каждой кнопки в виде ключей массивов:
     *
     * Имя ключа массива | Тип значения | Ключ массива, для которого доступна опция | Описание
     * ----------------- | ------------ | ----------------------------------------- | ------------
     * `urlGrid`         | `string`     | `create`, `update`, `delete`              | Ссылка грида [[GridView]], у которого выбирается запись
     * `inputName`       | `string`     | `create`, `delete`                        | Имя HTML атрибута `name` HTML элемента `input`, в который будет записываться `json` ключей выбранных записей. Используется при создании новой записи.
     * `beforeRender`    | `\Closure`   | `create`, `update`, `delete`              | Анонимная функция возвращающая `true` или `false`. Отображать кнопку в гриде или нет. Например для проверки прав доступа пользователя.
     */
    public $crudSettings = [];
    /**
     * @var array Панель-заголовок виджета.
     *
     * Содержит следующие опции ключей массива:
     *
     * Имя ключа массива | Тип значения | Описание
     * ----------------- | ------------ | ------------
     * `title`           | `string`     | Заголовок грида
     * `icon`            | `string`     | Иконка грида, пример: `FA::icon(FA::_HOME)`
     */
    public $panelHeading = [];
    /**
     * @var bool Активировать возможность выделения записей грида.
     */
    public $selectColumn = false;
    /**
     * @var bool Активировать столбец с нумерацией записей.
     */
    public $serialColumn = true;
    /**
     * @var bool|int Минимальная высота грида в пикселях, если `false`, то `height: auto`.
     */
    public $minHeight = false;
    /**
     * @var bool Активировать диалог настроек грида (Видимость колонок, порядок, количество отображаемых записей).
     */
    public $customizeDialog = true;
    /**
     * @var GWFilterDialogConfiguration|array Класс конфигурации дополнительных фильтров [[\common\widgets\GridView\services\GWFilterDialogConfiguration]], или конфигурационный массив.
     *
     * Конфигурационный массив содержит следующие опции ключи массива:
     *
     * Имя ключа массива | Тип значения                                                               | Описание
     * ----------------- | -------------------------------------------------------------------------- | ------------
     * `enable`          | `bool`                                                                     | Активировать кнопку с модальным окном дополнительного фильтра
     * `filterModel`     | [\yii\base\Model](https://www.yiiframework.com/doc/api/2.0/yii-base-model) | Модель дополнительного фильтра
     * `filterView`      | `string`                                                                   | Представление дополнительного фильтра, по умолчанию `_filter`
     */
    public $filterDialog = [
        'enable' => false,
    ];
    /**
     * @var GWExportGridConfiguration|array Класс конфигурации экспорта грида [[\common\widgets\GridView\services\GWExportGridConfiguration]], или конфигурационный массив.
     *
     * Конфигурационный массив содержит следующие опции ключи массива:
     *
     * Имя ключа массива | Тип значения | Описание
     * ----------------- | ------------ | ------------
     * `enable`          | `bool`       | Активировать кнопку с возможность экспорта грида
     * `format`          | array        | Доступные форматы для экспорта, доступно `[GridView::EXCEL, GridView::PDF]`
     * `idReportLoader`  | `string`     | HTML атрибут `id` обработчика отчетов [[\common\widgets\ReportLoader\ReportLoader]]
     */
    public $exportGrid = [
        'enable' => false,
    ];
    /**
     * @var array Переопределенный массив конфигурации `\kartik\grid\GridView`
     */
    public $toolbar = [];
    /**
     * @var string Доболнительный контент слева относительно верхней панели кнопок.
     */
    public $leftBottomToolbar = '';
    /**
     * @var string Доболнительный контент справа относительно верхней панели кнопок.
     */
    public $rightBottomToolbar = '';
    /**
     * @var array Переопределенный массив конфигурации `\kartik\grid\GridView`, изменена анимация ожидания загрузки грида.
     */
    public $pjaxSettings = [
        'loadingCssClass' => 'wk-widget-grid-loading',
        //  'loadingCssClass' => false,
        // 'options' => ['clientOptions' => ['async' => false]],
    ];
    /**
     * @var string Шаблон панелей грида
     */
    public $panelTemplate = <<< HTML
        <div class="{prefix}{type}">   
            {panelHeading}
            {panelBefore}
             <div class="wk-widget-grid-container">
                {items}
                <div class="wk-widget-grid-loading-container"></div>
             </div>
             
            {panelAfter}
            {panelFooter}   
        </div>
HTML;
    /**
     * @var string Шаблон нижней панели грида
     */
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
    /**
     * @var string Шаблон верхней панели грида
     */
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
            <div class="btn-toolbar pull-left kv-grid-toolbar wk-addition-grid-toolbar" role="toolbar">
                {toolbar}
            </div>
            <div class="btn-toolbar pull-right kv-grid-toolbar" role="toolbar">
                <div class="wk-custom-buttons">
                    {customButtons}
                </div>
            </div>
        </div>
        {before}
        <div class="clearfix"></div>
HTML;
    /** @var string Шаблон подвала грида */
    public $panelFooterTemplate = <<< HTML
        <div class="kv-panel-pager pull-left">
            {pager}
        </div>
        <div class="selectedPanel pull-right" style="display: none;"></div>
        {footer}
        <div class="clearfix"></div>
HTML;
    /**
     * @var string Шаблон заголовка грида
     */
    public $panelHeadingTemplate = <<< HTML
    <div class="pull-right">
        {summary}
    </div>
    <h3 class="panel-title">
        {heading}
    </h3>
    <div class="clearfix"></div>
HTML;
    /**
     * @var array Массив с контентом дополнительных кнопок грида
     */
    public $customButtons = [];
    /**
     * @var array Массив с контентом внутренних дополнительных кнопок грида
     */
    public $customButtonsInternal = [];
    /**
     * @var array Массив с контентом дополнительных кнопок действия грида
     */
    public $customActionButtons = [];
    /**
     * @var \Closure Анонимная функция для исключения уже выбранных записей.
     *
     * Содержит следующие параметры:
     * * `$activeQuery` - [\yii\db\ActiveQuery](https://www.yiiframework.com/doc/api/2.0/yii-db-activequery) Результирующего набора грида.
     * * `$ids` - Массив первичных ключей, выбранных записей, которые необходимо исключить из выборки.
     *
     * ```php
     *     ...
     *     'gridExcludeIdsFunc' => function (ActiveQuery $activeQuery, array $ids) {
     *         // Исключаем первичные ключи, находящиеся в массиве $ids
     *         $activeQuery->andWhere(['not in', 'build_id', $ids]);
     *     }
     *     ...
     * ```
     */
    public $gridExcludeIdsFunc;
    /**
     * @var \Closure Анонимная функция добавляет запись в грид при обновлении текущей записи, из другого грида.
     *
     * Конфигурационный массив содержит следующие опции ключи массива:
     *
     * Имя ключа массива     | Тип значения | Описание
     * --------------------- | ------------ | ------------
     * `mainField`           | `string`     | Имя атрибута к кому добавляем запись
     * `mainIdParameterName` | `string`     | Имя параметра значения атрибута для вывода в Url
     * `foreignField`        | `string`     | Имя атрибута значение, которое добавляем
     * `modelClassName`      | `string`     | Имя класса модели, которой добавляем запись
     * `saveFunc`            | `\Closure`   | Анонимная функция выполняющая сохранение, имеется по умолчанию
     *
     * *В данном коде Пользователю с ИД `user_id` добавляется роль `item_name`, выбранная в справочнике ролей*
     *
     * ```php
     *     ...
     *     'gridInject' => [
     *         'mainField' => 'user_id',
     *         'mainIdParameterName' => 'id',
     *         'foreignField' => 'item_name',
     *         'modelClassName' => 'domain\models\base\AuthAssignment',
     *         'saveFunc' => function (\yii\db\ActiveRecord $model, $mainId, $mainField, $foreignField, $foreignId) {
     *             $role = $Yii::$app->authManager->getRole($foreignId);
     *
     *             if (!Yii::$app->authManager->assign($role, $mainId)) {
     *                 throw new \DomainException('Saving error.');
     *             }
     *         },
     *     ],
     *     ...
     * ```
     *
     * **Функция `saveFunc` по умолчанию:**
     *
     * ```php
     *     function (\yii\db\ActiveRecord $model, $mainId, $mainField, $foreignField, $foreignId) {
     *         $model->$mainField = $mainId;
     *         $model->$foreignField = $foreignId;
     *         $model->save();
     *     };
     * ```
     */
    public    $gridInject;
    protected $js = [];
    /** @var GWCustomizeDialog */
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
        if ($this->customizeDialog) {
            GWCustomizeDialog::lets($this)->prepareConfig()->makeColumnsContent();
        }
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
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

    // переопределен изза глюка с руссификацией kartik GridView
    public function renderSummary()
    {
        $count = $this->dataProvider->getCount();
        if ($count <= 0) {
            return '';
        }
        $summaryOptions = $this->summaryOptions;
        $tag = ArrayHelper::remove($summaryOptions, 'tag', 'div');
        if (($pagination = $this->dataProvider->getPagination()) !== false) {
            $totalCount = $this->dataProvider->getTotalCount();
            $begin = $pagination->getPage() * $pagination->pageSize + 1;
            $end = $begin + $count - 1;
            if ($begin > $end) {
                $begin = $end;
            }
            $page = $pagination->getPage() + 1;
            $pageCount = $pagination->pageCount;
            if (($summaryContent = $this->summary) === null) {
                return Html::tag($tag, Yii::t('yii', 'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{item} other{items}}.', [
                    'begin' => $begin,
                    'end' => $end,
                    'count' => $count,
                    'totalCount' => $totalCount,
                    'page' => $page,
                    'pageCount' => $pageCount,
                    'item' => $this->itemLabelSingle,
                    'items' => $this->itemLabelPlural,
                ]), $summaryOptions);
            }
        }

        return parent::renderSummary();
    }

    protected function endPjax()
    {
        $view = $this->getView();
        $view->registerJs(file_get_contents(Yii::getAlias('@npm') . '/propellerkit/components/button/js/ripple-effect.js'));
        $view->registerJs(strtr(file_get_contents(Yii::getAlias('@common') . '/widgets/PropellerAssets/assets/js/dropdown.js'), ['wkGridView' => $this->id]));
        if ($this->filterDialog->isEnable()) {
            $view->registerJs(file_get_contents(Yii::getAlias('@npm') . '/propellerkit/components/textfield/js/textfield.js'));
            $view->registerJs(file_get_contents(Yii::getAlias('@npm') . '/propellerkit/components/checkbox/js/checkbox.js'));
        }

        parent::endPjax();
    }

    public function registerJs($script)
    {
        $this->js[] = $script;
    }

    protected function setDefaults()
    {
        if ($this->id === static::$autoIdPrefix . (static::$counter - 1)) {
            $this->id = ($this->filterModel instanceof Model ? $this->filterModel->formName() : $this->id) . 'Grid';
        }

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
        $customActionButtons = $this->customActionButtons;
        $customActionButtonsTemplate = '';

        if ($actionButtons->exists() || $customActionButtons) {
            if ($customActionButtons) {
                $customActionButtonsTemplate = $customActionButtons ? '{' . implode('} {', array_keys($customActionButtons)) . '}' : '';
            }

            array_unshift($this->columns, [
                'class' => 'kartik\grid\ActionColumn',
                'header' => Html::encode('Действия'),
                'contentOptions' => ['class' => 'wk-grid-action-buttons'],
                'buttons' => array_merge($actionButtons->getButtons(), $customActionButtons),
                'template' => $actionButtons->template() . $customActionButtonsTemplate,
                'options' => ['wk-widget' => true],
                'visible' => true,
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
        } else {
            $this->panel['heading'] = '';
        }
    }

    protected function registerAssetsByWk()
    {
        $view = $this->getView();
        GridViewAsset::register($view);

        foreach ($this->js as $script) {
            $view->registerJs($script);
        }

        PropellerAsset::setWidget(self::className());
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
                $("#$id").yiiGridView({
                    "filterUrl": window.location.search,
                    "filterSelector": "#$id-filters input, #$id-filters select"
                }); /* сокращает url purifyingUrl() */
                
                function Func_$id() {
                    var busy = false;
                    $.each($("div[data-pjax-container]"), function() {
                        if ($(this)[0].busy) {
                            busy = $(this)[0].busy;
                        }
                    });
                
                    if (busy === false) {
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
        $this->customButtons = array_merge($this->customButtonsInternal, $this->customButtons);

        if ($this->customButtons) {
            $buttons = '';
            array_map(function ($liContent) use (&$buttons) {
                if ($liContent === '{divider}') {
                    $buttons .= '<li role="presentation" class="divider"></li>';
                } else {
                    $buttons .= "<li role=\"presentation\">$liContent</li>";
                }
            }, $this->customButtons);

            $this->panelBeforeTemplate = strtr($this->panelBeforeTemplate, ['{customButtons}' => <<<EOT
                <span class="dropdown pmd-dropdown">
                    <button class="btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect" type="button" data-toggle="dropdown" aria-expanded="true"><i class="glyphicon glyphicon-option-vertical"></i></button>
                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                         $buttons
                    </ul>
                </span>
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