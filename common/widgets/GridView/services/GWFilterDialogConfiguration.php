<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 16.06.2017
 * Time: 17:39
 */

namespace common\widgets\GridView\services;


use yii\base\Model;

class GWFilterDialogConfiguration
{
    protected $enable = false;
    /** @var  Model|null */
    protected $filterModel;
    protected $filterView = '_filter';

    public function __construct($config = [])
    {
        if ($config['enable'] !== false && !($config['filterModel'] instanceof Model)) {
            throw new \Exception('filterModel() method required');
        }

        if ($config['enable'] !== false && isset($config['filterModel'])) {
            $this->enable = true;
            $this->filterModel = $config['filterModel'];
            if (is_string($config['filterView']) && !empty($config['filterView'])) {
                $this->filterView = $config['filterView'];
            }
        }
    }

    public function isEnable()
    {
        return $this->enable;
    }

    public function getFilterModel()
    {
        return $this->filterModel;
    }

    public function getFilterView()
    {
        return $this->filterView;
    }
}