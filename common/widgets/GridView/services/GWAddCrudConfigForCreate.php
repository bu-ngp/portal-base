<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 07.07.2017
 * Time: 8:45
 */

namespace common\widgets\GridView\services;


class GWAddCrudConfigForCreate
{
    protected $urlGrid;
    protected $inputName;

    public function __construct($config = [])
    {
        if (!is_string($config['urlGrid']) && !is_array($config['urlGrid'])) {
            throw new \Exception('urlGrid variable must be string or array');
        }

        if (!is_string($config['inputName'])) {
            throw new \Exception('inputName variable must be string');
        }

        if (empty($config['urlGrid'])) {
            throw new \Exception('urlGrid variable required');
        }

        if (empty($config['inputName'])) {
            throw new \Exception('inputName variable required');
        }

        $this->urlGrid = $config['urlGrid'];
        $this->inputName = $config['inputName'];
    }

    /**
     * @return array|string
     */
    public function getUrlGrid()
    {
        return $this->urlGrid;
    }

    /**
     * @return string
     */
    public function getInputName()
    {
        return $this->inputName;
    }
}