<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.05.2017
 * Time: 8:51
 */

namespace common\widgets\GridView\services;


use common\widgets\GridView\GridView;
use yii\bootstrap\Html;

class GWPrepareColumns
{
    private $config;
    private $configColumns;
    private $columns;

    public static function lets($config)
    {
        return new self($config);
    }

    public function __construct($config)
    {
        $this->config = $config;
        $this->configColumns = $config['columns'];
        $this->columns = [];
    }

    public function prepare()
    {
        $this->columns = [];
        if (is_array($this->configColumns) && count($this->configColumns) > 0) {
            $this->serialColumn();
            $this->selectColumn();

            foreach ($this->configColumns as $key => $column) {
                if (is_array($column)) {
                    $this->addRequiredProperties($column);
                    $this->addTooltip($column);
                    $this->addValueToSpan($column);
                } else {
                    $this->addDataColumn($column);
                }

                $this->columns[] = $column;
            }

            foreach ($this->columns as $key => $column) {
                $this->columns[$key]['headerOptions'] = array_replace_recursive([
                    'wk-hash' => hash('crc32', $column['attribute'] . $key),
                ], isset($this->columns[$key]['headerOptions']) ?: []);
            }
        }

        return $this->columns;
    }

    protected function serialColumn()
    {
        $serialColumn = array_filter($this->configColumns, function ($column) {
            return is_array($column) && isset($column['class']) && $column['class'] === '\kartik\grid\SerialColumn' && isset($column['options']['wk-widget']) && $column['options']['wk-widget'];
        });

        if (empty($serialColumn)) {
            $this->columns[] = [
                'class' => '\kartik\grid\SerialColumn',
                'noWrap' => true,
                'options' => ['wk-widget' => true],
            ];
        }
    }

    protected function selectColumn()
    {
        if ($this->config['selectColumn']) {
            $selectColumn = array_filter($this->configColumns, function ($column) {
                return is_array($column) && isset($column['class']) && $column['class'] === '\kartik\grid\CheckboxColumn' && isset($column['options']['wk-widget']) && $column['options']['wk-widget'];
            });

            if (empty($selectColumn)) {
                $this->columns[] = [
                    'class' => '\kartik\grid\CheckboxColumn',
                    'noWrap' => true,
                    'rowSelectedClass' => GridView::TYPE_INFO,
                    'options' => ['wk-widget' => true],
                ];
            }
        }
    }

    protected function addTooltip(&$column)
    {
        if (is_callable($column['contentOptions'])) {
            $func = $column['contentOptions'];

            $column['contentOptions'] = function ($model, $key, $index, $column) use ($func) {
                $return = $func($model, $key, $index, $column);
                $return['class'] .= ' wk-nowrap';
                $return['data-toggle'] = 'tooltip';
                return $return;
            };
        } else {
            $column['contentOptions'] = function () {
                return ['data-toggle' => 'tooltip', 'class' => 'wk-nowrap'];
            };
        }
    }

    protected function addValueToSpan(&$column)
    {
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
                    return '<span>' . Html::encode($model->{$column->attribute}) . '</span>';
                };
            }
        }
    }

    protected function addDataColumn(&$column)
    {
        $column = [
            'attribute' => $column,
            'class' => '\kartik\grid\DataColumn',
            'noWrap' => true,
            'contentOptions' => function () {
                return ['data-toggle' => 'tooltip', 'class' => 'wk-nowrap'];
            },
            'format' => 'html',
            'value' => function ($model, $key, $index, $column) {
                return '<span>' . Html::encode($model->{$column->attribute}) . '</span>';
            },
        ];
    }

    private function addRequiredProperties(&$column)
    {
        if (empty($column['class'])) {
            $column['class'] = '\kartik\grid\DataColumn';
        }

        if (empty($column['noWrap'])) {
            $column['noWrap'] = true;
        }
    }

}