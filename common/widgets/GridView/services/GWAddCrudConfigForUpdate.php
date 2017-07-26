<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 07.07.2017
 * Time: 8:45
 */

namespace common\widgets\GridView\services;


class GWAddCrudConfigForUpdate implements GWConfigInterface
{
    public $urlGrid;
    public $urlAction;

    public static function set()
    {
        return new self();
    }

    public function urlGrid($urlGrid)
    {
        $this->urlGrid = $urlGrid;
        return $this;
    }

    public function urlAction($urlAction)
    {
        $this->urlAction = $urlAction;
        return $this;
    }

    public function build()
    {
        if (!is_string($this->urlGrid) && !is_array($this->urlGrid)) {
            throw new \Exception('urlGrid() must be string or array');
        }

        if (!is_string($this->urlAction) && !is_array($this->urlAction)) {
            throw new \Exception('urlAction() must be string or array');
        }

        if (empty($this->urlGrid)) {
            throw new \Exception('urlGrid() required');
        }

        if (empty($this->urlAction)) {
            throw new \Exception('urlAction() required');
        }

        return $this;
    }
}