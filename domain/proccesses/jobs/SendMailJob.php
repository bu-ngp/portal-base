<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.12.2017
 * Time: 12:42
 */

namespace domain\proccesses\jobs;

use yii\mail\MailerInterface;
use yii\queue\JobInterface;
use yii\queue\Queue;

class SendMailJob implements JobInterface
{
    public $from;
    public $to = [];
    public $subject;
    public $html;

    private $_mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->_mailer = $mailer;
    }

    /**
     * @param Queue $queue which pushed and is handling the job
     */
    public function execute($queue)
    {
        if ($this->from && $this->to && $this->subject && $this->html) {
            try {
                $this->_mailer->compose()
                    ->setFrom($this->from)
                    ->setTo($this->to)
                    ->setSubject($this->subject)
                    ->setHtmlBody($this->html)
                    ->send();
            } catch (\Exception $e) {

            }
        }
    }
}