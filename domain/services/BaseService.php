<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 12:23
 */

namespace domain\services;


class BaseService
{
    private $errorStore;

    public function __construct()
    {
        $this->errorStore = [];
    }

    public function addError($attribute, $message)
    {
        $this->errorStore = array_replace_recursive($this->errorStore, [$attribute => [$message]]);
    }

    public function getErrors()
    {
        return $this->errorStore;
    }

    public function getErrorsAsString()
    {
        $arr = $this->errorStore;
        array_walk($arr, function (&$value) {
            $value = implode(',', $value);
        });

        return implode(',', $arr);
    }

}