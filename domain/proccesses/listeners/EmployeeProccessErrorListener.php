<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.12.2017
 * Time: 11:03
 */

namespace domain\proccesses\listeners;


use doh\events\ProccessErrorEvent;
use domain\services\base\ConfigCommonService;
use Yii;
use yii\queue\Queue;

class EmployeeProccessErrorListener
{
    private $service;

    public function __construct(ConfigCommonService $service)
    {
        $this->service = $service;
    }

    public function handle(ProccessErrorEvent $event)
    {
        /** @var Queue $query */
        $query = Yii::$app->get('queueExecutor');
        $job = Yii::createObject([
            'class' => 'domain\proccesses\jobs\SendMailJob',
            'from' => $this->service->getPortalMail(),
            'to' => $this->service->getAdministratorMails(),
            'subject' => 'Ошибка при импорте сотрудников на портал',
            'html' => "При импорте сотрудников на портал произошка ошибка: " . $event->exception->getMessage(),
        ]);

        $query->push($job);
    }
}