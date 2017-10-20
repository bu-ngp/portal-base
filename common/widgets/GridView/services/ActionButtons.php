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

class ActionButtons
{
    protected $createButton = '';
    protected $actionButtons = [];
    protected $grid;

    public function __construct(GridView $grid)
    {
        $this->grid = $grid;

        ActionButtonChoose::init($this->actionButtons, $grid);

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
        return $this->actionButtons ? '{' . implode("} {", array_keys($this->actionButtons)) . '}' : '';
    }

    public function getCreateButton()
    {
        return $this->createButton;
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