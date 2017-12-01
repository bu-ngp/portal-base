<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 17:03
 */

namespace domain\rules\base;


class ConfigCommonRules
{
    public static function client()
    {
        return
            [
                [['config_common_portal_mail'], 'email'],
                [['config_common_mail_administrators'], 'string', 'max' => 255],
            ];
    }
}