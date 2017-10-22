<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 11:16
 */

namespace domain\rules\base;


use common\classes\validators\WKDateValidator;

class ParttimeRules
{
    public static function client()
    {
        return [
            [['!person_id', 'dolzh_id', 'podraz_id', 'parttime_begin'], 'required'],
            [['parttime_begin', 'parttime_end'], WKDateValidator::className()],
        ];
    }
}