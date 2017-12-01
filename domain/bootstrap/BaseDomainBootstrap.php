<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 17:21
 */

namespace domain\bootstrap;

use doh\services\classes\ProcessLoader;
use domain\proccesses\EmployeeProccessLoader;
use yii\base\BootstrapInterface;
use yii;

class BaseDomainBootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

        $container->setSingleton('yii\mail\MailerInterface', function () use ($app) {
            return $app->mailer;
        });

        yii\base\Event::on(EmployeeProccessLoader::className(), ProcessLoader::EVENT_PROCCESS_ERROR, function ($event) {
            $listener = Yii::createObject(['class' => 'domain\proccesses\listeners\EmployeeProccessErrorListener']);
            $listener->handle($event);
        });
    }
}