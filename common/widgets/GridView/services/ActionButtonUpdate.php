<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.09.2017
 * Time: 13:58
 */

namespace common\widgets\GridView\services;

use common\widgets\GridView\GridView;
use domain\helpers\GridViewHelper;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\Url;

class ActionButtonUpdate
{
    protected $actionButtons;
    protected $grid;
    protected $key;
    protected $crudProp;

    static public function init(&$actionButtons, GridView $grid, $key, $crudProp)
    {
        if ($key !== 'update') {
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
        $crudProp = $this->crudProp;

        $this->actionButtons['update'] = function ($url, $model) use ($crudProp) {
            $crudUrl = is_string($crudProp) ? $crudProp : $crudProp['url'];

            $customurl = Url::to([$crudUrl, 'id' => GridViewHelper::isBinary($model->primaryKey) ? Uuid::uuid2str($model->primaryKey) : $model->primaryKey]);
            return $this->beforeRender($model) ? Html::a('<i class="fa fa-2x fa-pencil-square-o"></i>', $customurl, ['title' => Yii::t('wk-widget-gridview', 'Update'), 'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-primary', 'data-pjax' => '0']) : '';
        };

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