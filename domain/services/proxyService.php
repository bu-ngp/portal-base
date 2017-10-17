<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 13:28
 */

namespace domain\services;

use Yii;

class proxyService
{
    private $serviceClass;
    private $storeErrors = [];
    private $useFlash;

    public function __construct($serviceClass, $useFlash = true)
    {
        $this->serviceClass = $serviceClass;
        $this->useFlash = $useFlash;
    }

    public function __call($method, $arguments)
    {
        try {
            $result = call_user_func_array([$this->serviceClass, $method], $arguments);
            
            return $result === null ?: $result;
        } catch (\DomainException $e) {
            if ($e->getMessage()) {
                $this->storeErrors[] = $e->getMessage();
                if ($this->useFlash) {
                    Yii::$app->session->addFlash('error', $e->getMessage());
                }
            }
        }

        return false;
    }

    public function getErrorsProxyService()
    {
        return $this->storeErrors;
    }
}