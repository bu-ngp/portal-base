<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 16.06.2017
 * Time: 17:39
 */

namespace common\widgets\GridView\services;


use yii\base\Model;

class GWFilterDialogConfig
{
    public $enable = true;
    /** @var  Model|null */
    public $filterModel;
    public $filterView = '_filter';

    public static function set()
    {
        return new self();
    }

    public function enable($enabled)
    {
        $this->enable = $enabled;
        return $this;
    }

    public function filterModel(Model $filterModel)
    {
        $this->filterModel = $filterModel;
        return $this;
    }

    public function filterView($filterView)
    {
        $this->filterView = $filterView;
        return $this;
    }

    public function build()
    {
        if (!is_bool($this->enable)) {
            throw new \Exception('enable() must be Boolean');
        }

        if ($this->enable && !($this->filterModel instanceof Model)) {
            throw new \Exception('filterModel() method required');
        }

        if (!is_string($this->filterView)) {
            throw new \Exception('filterView() must be string');
        }

        return $this;
    }
}