<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 16:28
 */

namespace ngp\services\rules;


class TilesRules
{
    public static function client()
    {
        return
            [
                [['tiles_name', 'tiles_link'], 'required'],
                [['tiles_name', 'tiles_keywords', 'tiles_link', 'tiles_thumbnail', 'tiles_icon', 'tiles_icon_color'], 'string', 'max' => 255],
                [['tiles_description'], 'string', 'max' => 400],
            ];
    }
}