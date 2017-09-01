<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.09.2017
 * Time: 13:24
 */

namespace common\widgets\GridView\services;


use common\widgets\GridView\GridView;
use Yii;
use yii\bootstrap\Html;

class ActionButtons
{
    protected $createButton = '';
    protected $actionButtons = [];
    protected $grid;

    public function __construct(GridView $grid)
    {
        $this->grid = $grid;

        $this->selectGridState();

        if (is_array($grid->crudSettings) && count($grid->crudSettings) > 0) {
            foreach ($grid->crudSettings as $key => $crudProp) {
                $this->guardCrudSettings($key);

                ActionButtonCreate::init($this->createButton, $grid, $key, $crudProp);
                ActionButtonUpdate::init($this->actionButtons, $grid, $key, $crudProp);
                ActionButtonDelete::init($this->actionButtons, $grid, $key, $crudProp);
            }
        }
    }

    public function exists()
    {
        return count($this->actionButtons) > 0;
    }

    public function getButtons()
    {
        return $this->actionButtons;
    }

    public function template()
    {
        return '{' . implode("} {", array_keys($this->actionButtons)) . '}';
    }

    public function getCreateButton()
    {
        return $this->createButton;
    }

    protected function selectGridState()
    {
        if ($this->grid->gridExcludeIdsFunc instanceof \Closure
            && Yii::$app->request->headers['wk-selected']
            && ($_selected = json_decode(Yii::$app->request->headers['wk-selected']))
            && (property_exists($_selected, 'exclude') || property_exists($_selected, 'reject'))
        ) {
            $this->actionButtons['choose'] = function ($url, $model) use ($_selected) {
                $url = $_selected->url . (preg_match('/\?/', $_selected->url) ? '&' : '?') . 'grid=' . urlencode($_selected->gridID) . '&selected=' . urlencode($model->primaryKey);

                return Html::a('<i class="fa fa-2x fa-check-square-o"></i>', $url, ['title' => Yii::t('wk-widget-gridview', 'Choose'), 'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0']);
            };

            $func = $this->grid->gridExcludeIdsFunc;

            $func($this->grid->dataProvider->query, property_exists($_selected, 'exclude') ? $_selected->exclude : [$_selected->reject]);
        }
    }

    protected function guardCrudSettings($key)
    {
        if (!in_array($key, ['create', 'update', 'delete'])) {
            new \Exception(Yii::t('wk-widget-gridview', "In 'crudOptions' array must be only this keys ['create', 'update', 'delete']. Passed '{key}'", [
                'key' => $key,
            ]));
        }
    }
}