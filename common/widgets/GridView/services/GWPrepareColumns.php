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
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class GWPrepareColumns
{
    /** @var GridView */
    private $gridView;
    private $columns;

    public static function lets($gridView)
    {
        return new self($gridView);
    }

    public function __construct($gridView)
    {
        $this->gridView = $gridView;
        $this->columns = [];
    }

    public function prepare()
    {
        $this->columns = [];
        if (is_array($this->gridView->columns) && count($this->gridView->columns) > 0) {
            $this->serialColumn();
            $this->selectColumn();

            foreach ($this->gridView->columns as $key => $column) {
                if (is_array($column)) {
                    $this->addRequiredProperties($column);
                    $this->addTooltip($column);
                    $this->addValueToSpan($column);

                } else {
                    $this->addDataColumn($column);
                }

                $this->addFilterDateFormat($column);
                $this->addFilterProperties($column);
                $this->addVisibleProperties($column);

                $this->columns[] = $column;
            }

            foreach ($this->columns as $key => $column) {
                $this->columns[$key]['headerOptions'] = array_replace_recursive([
                    'wk-hash' => hash('crc32', $column['attribute'] . $key),
                ], isset($this->columns[$key]['headerOptions']) ?: []);
            }

            $this->gridView->columns = $this->columns;
        }
    }

    protected function serialColumn()
    {
        $serialColumn = array_filter($this->gridView->columns, function ($column) {
            return is_array($column) && isset($column['class']) && $column['class'] === '\kartik\grid\SerialColumn' && isset($column['options']['wk-widget']) && $column['options']['wk-widget'];
        });

        if (empty($serialColumn)) {
            $this->columns[] = [
                'class' => '\kartik\grid\SerialColumn',
                'noWrap' => true,
                'options' => ['wk-widget' => true],
                'visible' => true,
            ];
        }
    }

    protected function selectColumn()
    {
        if ($this->gridView->selectColumn) {
            $selectColumn = array_filter($this->gridView->columns, function ($column) {
                return is_array($column) && isset($column['class']) && $column['class'] === 'common\widgets\GridView\services\CheckboxStorageColumn' && isset($column['options']['wk-widget']) && $column['options']['wk-widget'];
            });

            if (empty($selectColumn)) {
                $this->columns[] = [
                    'class' => 'common\widgets\GridView\services\CheckboxStorageColumn',
                    'noWrap' => true,
                    'rowSelectedClass' => GridView::TYPE_INFO,
                    'options' => ['wk-widget' => true],
                    'visible' => true,
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
            $column['contentOptions'] = function () use ($column) {
                if (is_array($column['contentOptions'])) {
                    $column['contentOptions']['data-toggle'] = 'tooltip';
                    $column['contentOptions']['class'] = trim(ArrayHelper::getValue($column, 'contentOptions.class') . ' wk-nowrap');

                    return $column['contentOptions'];
                }

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
                    /** @var $model ActiveRecord */
                    return '<span>' . Html::encode($model[$column->attribute]) . '</span>';
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
                /** @var $model ActiveRecord */
                try {
                    $resultValue = $model;
                    $splitAttributes = explode('.', $column->attribute);
                    array_walk($splitAttributes, function ($value) use (&$resultValue) {
                        $resultValue = $resultValue[$value];
                    });
                } catch (\Exception $e) {
                    $resultValue = $model[$column->attribute];
                }

                return '<span>' . Html::encode($resultValue) . '</span>';
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

    protected function addFilterProperties(&$column)
    {
        /** @var ActiveRecord $model */
        $model = $this->gridView->filterModel;
        if (method_exists($model, 'itemsValues') && $items = $model::itemsValues($column['attribute'])) {
            $column['filter'] = $items;
            $column['format'] = 'raw';
            $column['value'] = function ($model, $key, $index, $column) use ($items) {
                /** @var $model ActiveRecord */
                $value = $model[$column->attribute];

                return '<span key="' . $value . '">' . (isset($value) ? Html::encode($items[$value]) : '') . '</span>';
            };
        }
    }

    protected function addVisibleProperties(&$column)
    {
        $column['visible'] = !isset($column['visible']) || $column['visible'] === true;
    }

    protected function addFilterDateFormat(&$column)
    {
//        if ($column['format'] === 'datetime') {
//            $column['filterType'] = GridView::FILTER_DATE_RANGE;
//            $column['filterWidgetOptions']['pluginOptions']['locale']['format'] = 'DD.MM.YYYY HH:mm:ss';
//        }
//        if ($column['format'] === 'date') {
//            $column['filterType'] = GridView::FILTER_DATE_RANGE;
//            $column['filterWidgetOptions']['pluginOptions']['locale']['format'] = 'DD.MM.YYYY';
//         }
    }
}