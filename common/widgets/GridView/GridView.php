<?php

namespace common\widgets\GridView;

use common\widgets\GridView\assets\GridViewAsset;
use Yii;
use yii\bootstrap\Html;
use yii\web\View;

class GridView extends \kartik\grid\GridView
{

    public $crudSettings;
    public $customizeSettings;
    public $panelHeading;
    public $multipleSelect;

    public function __construct(array $config = [])
    {
        $this->registerTranslations();
        $config = $this->setDefaults($config);
        parent::__construct($config);
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
        parent::run();
    }

    protected function setDefaults($config)
    {
        $config['hover'] = isset($config['hover']) ? $config['hover'] : true;
        $config['pjax'] = isset($config['pjax']) ? $config['pjax'] : true;
        $this->multipleSelect = isset($config['multipleSelect']) ? $config['multipleSelect'] : true;
        $config['pjaxSettings']['loadingCssClass'] = isset($config['pjaxSettings']['loadingCssClass']) ? $config['pjaxSettings']['loadingCssClass'] : false;
        $config['resizableColumns'] = isset($config['resizableColumns']) ? $config['resizableColumns'] : false;
        $this->createCustomizeButtons($config['customizeSettings']);
        $this->createCrudButtons($config['crudSettings']);
        if (($key = array_search('{export}', $this->toolbar)) !== false) {
            unset($this->toolbar[$key]);
        }
        if (($key = array_search('{toggleData}', $this->toolbar)) !== false) {
            unset($this->toolbar[$key]);
        }
        $config['panelBeforeTemplate'] = isset($config['panelBeforeTemplate']) ? $config['panelBeforeTemplate'] : <<<EOT
            <div>
                <div class="btn-toolbar kv-grid-toolbar" role="toolbar" style="position: relative;">
                    {toolbar}
                </div>    
            </div>
            {before}
            <div class="clearfix"></div>
EOT;
        $this->setPanelHeading($config['panelHeading']);
        $config['columns'] = $this->prepareColumns($config['columns']);

        return $config;
    }

    protected function createCrudButtons($config)
    {
        if (is_array($config) && count($config) > 0) {
            $toolbar = [
                [
                    'content' => '',
                    'options' => ['class' => 'btn-group pull-left', 'style' => 'position: absolute; bottom: 0;'],
                ],
            ];

            $panelButtons = '';

            foreach ($config as $key => $crudUrl) {
                switch ($key) {
                    case 'create':
                        $button = Html::a(Yii::t('wk-widget-gridview', 'Create'), $crudUrl,
                            [
                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success',
                                'data-pjax' => '0'
                            ]);

                        $toolbar[0]['content'] .= $button;
                        $panelButtons .= $button;
                        break;
                    case 'update':
                        $button = Html::a(Yii::t('wk-widget-gridview', 'Update'), $crudUrl,
                            [
                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary',
                                'data-pjax' => '0'
                            ]);
                        $toolbar[0]['content'] .= $button;
                        $panelButtons .= $button;
                        break;
                    case 'delete':
                        $button = Html::a(Yii::t('wk-widget-gridview', 'Delete'), $crudUrl,
                            [
                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger',
                            ]);
                        $toolbar[0]['content'] .= $button;
                        $panelButtons .= $button;
                        break;
                    default:
                        new \Exception(Yii::t('wk-widget-gridview', "In 'crudOptions' array must be only this keys ['create', 'update', 'delete']. Passed '{key}'", [
                            'key' => $key,
                        ]));
                }
            }

            $panel = <<<EOT
                    <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
                        <div class="btn-group pull-left">
                            $panelButtons
                        </div>
                    </div>
EOT;
            $this->toolbar = array_merge_recursive($toolbar, $this->toolbar);
            $this->panel['after'] = $panel . $this->panel['after'];
        }
    }

    protected function createCustomizeButtons($config)
    {
        if (is_array($config) && count($config) > 0) {
            $toolbar = [
                [
                    'content' => '',
                    'options' => ['class' => 'btn-group-vertical btn-group-xs pull-right'],
                ],
            ];

            foreach ($config as $key => $option) {
                switch ($key) {
                    case 'filterShow':
                        $toolbar[0]['content'] .= Html::a(Yii::t('wk-widget-gridview', 'Filter'), '#',
                            [
                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary',
                                'style' => 'text-align: right;',
                            ]);
                        break;
                    case 'exportShow':
                        $toolbar[0]['content'] .= Html::a(Yii::t('wk-widget-gridview', 'Export'), '#',
                            [
                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-danger',
                                'style' => 'text-align: right;',
                            ]);
                        break;
                    case 'customizeShow':
                        $toolbar[0]['content'] .= Html::a(Yii::t('wk-widget-gridview', 'Customize'), '#',
                            [
                                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-default',
                                'style' => 'text-align: right;',
                            ]);
                        break;
                    default:
                        new \Exception(Yii::t('wk-widget-gridview', "In 'customizeSettings' array must be only this keys ['filterShow', 'exportShow', 'customizeShow']. Passed '{key}'", [
                            'key' => $key,
                        ]));
                }
            }
            $this->toolbar = array_merge_recursive($toolbar, $this->toolbar);
        }
    }

    protected function setPanelHeading($config)
    {
        if (is_array($config) && count($config) > 0) {
            $icon = '';
            $title = '';
            foreach ($config as $key => $option) {
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
            $this->panel['heading'] = '<h3 class="panel-title">' . $icon . $title . '</h3>';
        }
    }

    protected function registerAssets()
    {
        parent::registerAssets();
        $view = $this->getView();
        GridViewAsset::register($view);

        $options = [
            'selectionStorage' => true,
        ];

        $options = (object)array_filter($options);
        $optionsReplaced = str_replace('object', json_encode($options, JSON_UNESCAPED_UNICODE), file_get_contents(__DIR__ . '/assets/js/init.js'));
        $idReplaced = str_replace('id-widget', $this->id, $optionsReplaced);
        $view->registerJs($idReplaced, View::POS_END);
    }

    protected function prepareColumns($config)
    {
        if (is_array($config) && count($config) > 0) {
            $serialExist = false;
            foreach ($config as $key => $column) {
                if (is_array($column)) {
                    $column['noWrap'] = true;
                    if (empty($column['class'])) {
                        $column['class'] = '\kartik\grid\DataColumn';
                    }

                    if (empty($column['class'])) {
                        $serialExist = true;
                    }

                    if (is_callable($column['contentOptions'])) {
                        $func = $column['contentOptions'];

                        $column['contentOptions'] = function ($model, $key, $index, $column) use ($func) {
                            $return = $func($model, $key, $index, $column);
                            $return['class'] .= ' wk-nowrap';
                            $return['data-toggle'] = 'tooltip';
                            return $return;
                        };

                    } elseif (empty($column['contentOptions'])) {
                        $column['contentOptions'] = function ($model, $key, $index, $column) {
                            return ['data-toggle' => 'tooltip', 'class' => 'wk-nowrap'];
                        };
                    }

                    if ($column['class'] === '\kartik\grid\DataColumn' && (empty($column['format']))) {
                        $column['format'] = 'html';
                        if (is_callable($column['value'])) {
                            $func = $column['value'];
                            $column['value'] = function ($model, $key, $index, $column) use ($func) {
                                $return = $func($model, $key, $index, $column);
                                return '<span>' . Html::encode($return) . '</span>';
                            };

                        } elseif (empty($column['value'])) {
                            $column['value'] = function ($model, $key, $index, $column) {
                                $a = '';
                                return '<span>' . Html::encode($model->{$column->attribute}) . '</span>';
                            };
                        }
                    }
                } else {
                    $column = [
                        'attribute' => $column,
                        'class' => '\kartik\grid\DataColumn',
                        'noWrap' => true,
                        'contentOptions' => function ($model, $key, $index, $column) {
                            return ['data-toggle' => 'tooltip', 'class' => 'wk-nowrap'];
                        },
                        'format' => 'html',
                        'value' => function ($model, $key, $index, $column) {
                            return '<span>' . Html::encode($model->{$column->attribute}) . '</span>';
                        },
                    ];
                }

                $config[$key] = $column;
            }

            if ($this->multipleSelect) {
                $config = array_merge_recursive([[
                    'class' => '\kartik\grid\CheckboxColumn',
                    'noWrap' => true,
                    'rowSelectedClass' => GridView::TYPE_INFO,
                ]], $config);
            }

            if (!$serialExist) {
                $config = array_merge_recursive([[
                    'class' => '\kartik\grid\SerialColumn',
                    'noWrap' => true,
                ]], $config);
            }
        }
        return $config;
    }

}