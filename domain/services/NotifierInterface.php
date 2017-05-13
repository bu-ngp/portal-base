<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:24
 */

namespace domain\services;


interface NotifierInterface
{
    public function notify($email, $view, $data, $subject);
}