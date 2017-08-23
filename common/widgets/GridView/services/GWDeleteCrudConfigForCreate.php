<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 07.07.2017
 * Time: 8:45
 */

namespace common\widgets\GridView\services;


class GWDeleteCrudConfigForCreate implements GWConfigInterface
{
    public $inputName;

    public static function set()
    {
        return new self();
    }

    public function inputName($inputName)
    {
        $this->inputName = $inputName;
        return $this;
    }

    public function build()
    {
        if (!is_string($this->inputName)) {
            throw new \Exception('inputName() must be string');
        }

        if (empty($this->inputName)) {
            throw new \Exception('inputName() required');
        }

        return $this;
    }
}