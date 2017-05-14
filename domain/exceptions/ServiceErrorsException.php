<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 12:44
 */

namespace domain\exceptions;


use Exception;

class ServiceErrorsException extends \RuntimeException
{
    private $attributeName;

    public function __construct($attributeName, $message = "", $code = 0, Exception $previous = null)
    {
        $this->attributeName = $attributeName;
        parent::__construct($message, $code, $previous);
    }

    public function getAttribute()
    {
        return $this->attributeName;
    }

}