<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 10:00
 */

namespace domain\rules\base;

use Yii;

class RoleRules
{
    public static function client()
    {
        return
            [
                [['description'], 'required'],
                [['description'], 'string'],
                [['ldap_group'], 'string', 'max' => 255],
            ];
    }
}