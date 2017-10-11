<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 11.10.2017
 * Time: 13:41
 */

namespace common\widgets\GridView\services;


class GridViewHelper
{
    public static function isBinary($value)
    {
        return preg_match('~[^\x20-\x7E\t\r\n]~', $value) > 0;
    }
}