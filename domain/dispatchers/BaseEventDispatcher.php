<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:13
 */

namespace domain\dispatchers;

use domain\events\BaseEvent;

class BaseEventDispatcher implements EventDispatcherInterface
{
    private $listeners = [];

    public function __construct(array $listeners)
    {
        $this->listeners = $listeners;
    }

    public function dispatch(BaseEvent $event)
    {
        $eventName = get_class($event);
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listenerClass) {
                call_user_func([\Yii::createObject($listenerClass), 'handle'], $event);
            }
        }
    }
}