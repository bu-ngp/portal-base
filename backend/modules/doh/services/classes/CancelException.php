<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.11.2017
 * Time: 19:09
 */

namespace doh\services\classes;

class CancelException extends \Exception
{
    public function __construct()
    {
        parent::__construct("", 0, null);
    }
}