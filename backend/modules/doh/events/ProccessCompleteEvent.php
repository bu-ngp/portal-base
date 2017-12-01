<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.12.2017
 * Time: 10:47
 */

namespace doh\events;


use yii\base\Event;

class ProccessCompleteEvent extends Event
{
    public $handlerStatus;
    public $handlerAt;
    public $handlerDescription;
    public $handlerDoneTime;
    public $handlerUsedMemory;
    public $handlerPercent;
    public $handlerShortReport;
    public $handlerFiles;
}