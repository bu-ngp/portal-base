<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 12:33
 */

namespace domain\rules\base;

class BuildRules
{
    public static function client()
    {
        return [
            [['build_name'], 'required'],
            [['build_name'], 'string', 'max' => 255],
        ];
    }
}