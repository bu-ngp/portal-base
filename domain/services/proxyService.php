<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 13:28
 */

namespace domain\services;


use domain\exceptions\ServiceErrorsException;

class proxyService
{
    private $serviceClass;

    public function __construct(BaseService $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    public function __call($method, $arguments)
    {
        try {
            return call_user_func_array([$this->serviceClass, $method], $arguments);
        } catch (ServiceErrorsException $e) {
            $this->serviceClass->addError($e->getAttribute(), $e->getMessage());
        }
    }
}