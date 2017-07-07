<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 07.07.2017
 * Time: 8:45
 */

namespace common\widgets\GridView\services;


class GWCreateCrudConfig implements GWConfigInterface
{
    public $urlGrid;
    public $inputName;

    public static function set()
    {
        return new self();
    }

    public function urlGrid($urlGrid)
    {
        $this->urlGrid = $urlGrid;
        return $this;
    }

    public function inputName($inputName)
    {
        $this->inputName = $inputName;
        return $this;
    }

    public function build()
    {
        if (!is_string($this->urlGrid) && !is_array($this->urlGrid)) {
            throw new \Exception('urlGrid() must be string or array');
        }

        if (!is_string($this->inputName)) {
            throw new \Exception('inputName() must be string');
        }

        if (empty($this->urlGrid)) {
            throw new \Exception('urlGrid() required');
        }

        if (empty($this->inputName)) {
            throw new \Exception('inputName() required');
        }

        return $this;
    }
}