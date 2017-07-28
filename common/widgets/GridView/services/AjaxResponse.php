<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 27.07.2017
 * Time: 10:50
 */

namespace common\widgets\GridView\services;


class AjaxResponse
{
    const SUCCESS = 'success';
    const ERROR = 'error';

    public $result;
    public $message;

    public function __construct($result, $message = '')
    {
        $this->result = $result;
        $this->message = $message;
    }

    public static function init($result, $message = '')
    {
        return new self($result, $message);
    }
}