<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 12:27
 */

namespace domain\rules\base;


class DolzhRules
{
    public static function client()
    {
        return [
            [['dolzh_name'], 'required'],
            [['dolzh_name'], 'string', 'max' => 255],
        ];
    }
}