<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.12.2017
 * Time: 10:47
 */

namespace doh\events;


use yii\base\Event;

class ProccessErrorEvent extends Event
{
    public $handlerAt;
    public $handlerDescription;
    public $handlerPercent;
    /** @var \Exception */
    public $exception;
    public $eventData;
}