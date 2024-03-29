<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.05.2017
 * Time: 8:51
 */

namespace common\widgets\GridView\services;

use common\widgets\GridView\GridView;
use domain\services\SearchModel;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Класс подготавливает конфигурационный массив колонок для [[\common\widgets\GridView\GridView]].
 */
class GWPrepareColumns
{
    /** @var GridView */
    private $gridView;
    private $columns;

    /**
     * Метод создает экземпляр текущего класса
     *
     * @param GridView $gridView Грид [[\common\widgets\GridView\GridView]]
     * @return $this
     */
    public static function lets($gridView)
    {
        return new self($gridView);
    }

    /**
     * Конструктор класса.
     *
     * @param $gridView
     */
    public function __construct($gridView)
    {
        $this->gridView = $gridView;
        $this->columns = [];
    }

    /**
     * Метод преобразования и подготовки конфигурационного массива колонок для дальнейшего использования в [[\common\widgets\GridView\GridView]].
     */
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
                    $this->addFilterProperties($column);
                    $this->addValueToSpan($column);
                } else {
                    $this->addFilterProperties($column);
                    $this->addDataColumn($column);
                }

                $this->addFilterDateFormat($column);
                $this->addVisibleProperties($column);

                $this->columns[] = $column;
            }

            foreach ($this->columns as $key => $column) {
                $this->columns[$key]['headerOptions'] = array_replace_recursive([
                    'wk-hash' => hash('crc32', $column['attribute'] . $key),
                ], isset($this->columns[$key]['headerOptions']) ? $this->columns[$key]['headerOptions'] : []);
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
        if ($column['noWrap']) {
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
                    return '<span>' . Html::encode(ArrayHelper::getValue($model, $column->attribute)) . '</span>';
                };
            }
        }
    }

    protected function addDataColumn(&$column)
    {
        if (is_array($column)) {
            $column['class'] = '\kartik\grid\DataColumn';
            $column['noWrap'] = ArrayHelper::getValue($column, 'noWrap', true);
            $column['contentOptions'] = function () {
                return ['data-toggle' => 'tooltip', 'class' => 'wk-nowrap'];
            };
        } else {
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
                        $resultValue = ArrayHelper::getValue($model, $column->attribute);
                    }

                    return '<span>' . Html::encode($resultValue) . '</span>';
                },
            ];
        }
    }

    private function addRequiredProperties(&$column)
    {
        $column['class'] = ArrayHelper::getValue($column, 'class', '\kartik\grid\DataColumn');
        $column['noWrap'] = ArrayHelper::getValue($column, 'noWrap', true);
    }

    protected function addFilterProperties(&$column)
    {
        /** @var SearchModel $model */
        $model = $this->gridView->filterModel;//->activeRecord();
        $attribute = isset($column['attribute']) ? $column['attribute'] : $column;

        if ((isset($column['class']) && $column['class'] === '\kartik\grid\DataColumn' || is_string($column)) && preg_match('/\./', $attribute)) {
            $model = $model::activeRecord();
            $relatedPath = preg_replace('/(.*)\.(\w+)/', '$1', $attribute);
            $attribute = preg_replace('/(.*)\.(\w+)/', '$2', $attribute);

            $relatedArray = explode('.', $relatedPath);
            array_walk($relatedArray, function ($arString) use (&$model) {
                /** @var ActiveRecord $model */
                $relation = $model->getRelation($arString);
                $model = new $relation->modelClass;
            });
        }

        if (is_array($column) && isset($column['class']) && $column['class'] === '\kartik\grid\DataColumn') {
            if (
                (isset($column['value']) && !is_callable($column['value']) || !isset($column['value']))
                && method_exists($model, 'itemsValues')
                && ($items = $model::itemsValues($attribute))
            ) {
                $column['filter'] = $items;
                $column['format'] = 'raw';
                $column['value'] = function ($model, $key, $index, $column) use ($items) {
                    /** @var $model ActiveRecord */
                    $value = ArrayHelper::getValue($model, $column->attribute);

                    return '<span key="' . $value . '">' . (isset($value) ? Html::encode($items[$value]) : '') . '</span>';
                };
            }
        }

        if (is_string($column) && method_exists($model, 'itemsValues') && ($items = $model::itemsValues($attribute))) {
            $column = [
                'attribute' => $column,
                'filter' => $items,
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) use ($items) {
                    /** @var $model ActiveRecord */
                    $value = ArrayHelper::getValue($model, $column->attribute);

                    return '<span key="' . $value . '">' . (isset($value) ? Html::encode($items[$value]) : '') . '</span>';
                },
            ];
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

    protected function getValueCallable($value, $encode = false)
    {
        if (is_callable($value)) {
            return function ($model, $key, $index, $column) use ($value, $encode) {
                $return = $value($model, $key, $index, $column);
                return '<span>' . ($encode ? Html::encode($return) : $return) . '</span>';
            };
        }

        return $value;
    }
}