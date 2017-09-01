<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 07.07.2017
 * Time: 8:45
 */

namespace common\widgets\GridView\services;


class GWDeleteCrudConfigForCreate
{
    protected $inputName;

    public function __construct($config=[])
    {
        if (!is_string($config['inputName'])) {
            throw new \Exception('inputName variable must be string');
        }

        if (empty($config['inputName'])) {
            throw new \Exception('inputName variable required');
        }

        $this->inputName = $config['inputName'];
    }

    /**
     * @return string
     */
    public function getInputName()
    {
        return $this->inputName;
    }
}