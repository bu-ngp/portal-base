<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 17:21
 */
namespace domain\bootstrap;

use yii\base\BootstrapInterface;
use yii;
use yii\di\Container;

class BaseDomainBootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

    }
}