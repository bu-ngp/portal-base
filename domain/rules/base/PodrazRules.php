<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 12:34
 */

namespace domain\rules\base;

class PodrazRules
{
    public static function client()
    {
        return [
            [['podraz_name'], 'required'],
            [['podraz_name'], 'string', 'max' => 255],
        ];
    }
}