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

/**
 * Класс кнопок действия `CRUD`
 */
class ActionButtons
{
    protected $createButton = '';
    protected $actionButtons = [];
    protected $grid;

    /**
     * Конструктор класса, инициализация кнопок действий.
     *
     * @param GridView $grid Грид [[\common\widgets\GridView\GridView]]
     */
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

    /**
     * Проверяет на существование кнопок действий.
     *
     * @return bool
     */
    public function exists()
    {
        return count($this->actionButtons) > 0;
    }

    /**
     * Возвращает конфигурационный массив кнопок действий.
     *
     * @return array Массив мновок действий.
     */
    public function getButtons()
    {
        return $this->actionButtons;
    }

    /**
     * Возвращает строку-шаблон расположения кнопок действий.
     *
     * @return string Строка-шаблон вывода кнопок действия
     */
    public function template()
    {
        return $this->actionButtons ? '{' . implode("} {", array_keys($this->actionButtons)) . '}' : '';
    }

    /**
     * Возвращает контент кнопки добавления новой записи в грид.
     *
     * @return string
     */
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