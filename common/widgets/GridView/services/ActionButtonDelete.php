<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.09.2017
 * Time: 13:58
 */

namespace common\widgets\GridView\services;

use common\widgets\GridView\GridView;
use domain\helpers\BinaryHelper;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\Url;

/**
 * Класс кнопки действия грида "Удалить" запись
 */
class ActionButtonDelete
{
    protected $actionButtons;
    protected $grid;
    protected $key;
    protected $crudProp;

    /**
     * Создать экземпляр класса
     *
     * @param array $actionButtons Конфигурационный массив кнопок `CRUD`
     * @param GridView $grid Грид [[\common\widgets\GridView\GridView]]
     * @param string $key Ключ конфигурационного массива `CRUD`
     * @param array $crudProp Значение конфигурационного массива `CRUD` с ключом $key
     * @return bool|$this
     */
    static public function init(&$actionButtons, GridView $grid, $key, $crudProp)
    {
        if ($key !== 'delete') {
            return false;
        }

        return new self($actionButtons, $grid, $key, $crudProp);
    }

    protected function __construct(&$actionButtons, GridView $grid, $key, $crudProp)
    {
        $this->actionButtons = $actionButtons;
        $this->grid = $grid;
        $this->key = $key;
        $this->crudProp = $crudProp;

        $actionButtons = array_merge($actionButtons, $this->buttonInit());
    }

    protected function buttonInit()
    {
        $options = [
            'title' => Yii::t('wk-widget-gridview', 'Delete'),
            'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-gridview-crud-delete',
            'data-pjax' => '0'
        ];

        $isTypeObject = isset($this->crudProp['inputName']);

        if ($isTypeObject) {
            $GWDeleteCrud = Yii::createObject('common\widgets\GridView\services\GWDeleteCrudConfigForCreate', [[
                'urlGrid' => $this->crudProp['urlGrid'],
                'inputName' => $this->crudProp['inputName'],
            ]]);

            $options = array_merge($options, [
                'input-name' => $GWDeleteCrud->getInputName(),
            ]);

            $this->actionButtons['delete'] = function ($url, $model) use ($options) {
                $options = array_merge($options, [
                    'wk-id' => BinaryHelper::isBinary($model->primaryKey) ? Uuid::uuid2str($model->primaryKey) : $model->primaryKey,
                ]);

                return $this->beforeRender($model) ? Html::a('<i class="fa fa-2x fa-trash-o"></i>', '#', $options) : '';
            };
        } else {
            if (!is_array($this->crudProp)) {
                $this->crudProp = [$this->crudProp];
            }

            $crudProp = $this->crudProp;

            $this->actionButtons['delete'] = function ($url, $model) use ($crudProp, $options) {
                $urlArr = count($crudProp) === 1 && isset($crudProp[0]) ? $crudProp : [$crudProp['url']];

                $urlArr['id'] = BinaryHelper::isBinary($model->primaryKey) ? Uuid::uuid2str($model->primaryKey) : $model->primaryKey;
                $urlArr['mainId'] = Yii::$app->request->get('id');

                return $this->beforeRender($model) ? Html::a('<i class="fa fa-2x fa-trash-o"></i>', Url::to($urlArr), $options) : '';
            };
        }

        return $this->actionButtons;
    }

    protected function beforeRender($model)
    {
        if (!empty($this->crudProp['url']) && $this->crudProp['beforeRender'] instanceof \Closure) {
            return $this->crudProp['beforeRender']($model);
        }

        return true;
    }
}