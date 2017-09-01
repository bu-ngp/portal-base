<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 07.07.2017
 * Time: 8:45
 */

namespace common\widgets\GridView\services;


class GWAddCrudConfigForUpdate
{
    protected $urlGrid;

    public function __construct($config = [])
    {
        if (!is_string($config['urlGrid']) && !is_array($config['urlGrid'])) {
            throw new \Exception('urlGrid variable must be string or array');
        }

        if (empty($config['urlGrid'])) {
            throw new \Exception('urlGrid variable required');
        }

        $this->urlGrid = $config['urlGrid'];
    }

    /**
     * @return mixed|string
     */
    public function getUrlGrid()
    {
        return $this->urlGrid;
    }

}