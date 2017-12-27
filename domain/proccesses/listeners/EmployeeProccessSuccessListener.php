<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.12.2017
 * Time: 11:03
 */

namespace domain\proccesses\listeners;


use doh\events\ProccessSuccessEvent;
use domain\services\base\ConfigCommonService;
use Yii;
use yii\queue\Queue;

class EmployeeProccessSuccessListener
{
    private $service;

    public function __construct(ConfigCommonService $service)
    {
        $this->service = $service;
    }

    public function handle(ProccessSuccessEvent $event)
    {
        $view = Yii::$app->getView();

        if ($event->eventData['result']['error'] > 0) {
            /** @var Queue $queue */
            $queue = Yii::$app->get('queueExecutor');
            $job = Yii::createObject([
                'class' => 'domain\proccesses\jobs\SendMailJob',
                'from' => $this->service->getPortalMail(),
                'to' => $this->service->getAdministratorMails(),
                'subject' => 'Отчет по импорту сотрудников',
                'attachmentPaths' => ['Результат импорта.xlsx' => $event->eventData['result']['reportPath']],
                'html' => $view->render('@domain/proccesses/listeners/views/_employee_success_email', ['result' => $event->eventData['result']]),
            ]);

            $queue->push($job);
        }
    }
}