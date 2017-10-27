<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 11.10.2017
 * Time: 13:41
 */

namespace domain\helpers;


class GridViewHelper
{
    public static function isBinary($value)
    {
        return preg_match('~[^\x20-\x7E\t\r\n]~', $value) > 0;
    }

    public static function isBinaryValidString($string) {
        return ctype_xdigit($string) && mb_strlen($string, 'UTF-8') === 32;
    }
}