<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:12
 */

namespace domain\dispatchers;

use domain\events\BaseEvent;

interface EventDispatcherInterface
{
    public function dispatch(BaseEvent $event);
}