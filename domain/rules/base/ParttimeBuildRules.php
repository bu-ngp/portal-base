<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 11:16
 */

namespace domain\rules\base;


use domain\validators\WKDateValidator;

class ParttimeBuildRules
{
    public static function client()
    {
        return [
            [['build_id'], 'required'],
            [['parttime_id'], 'integer'],
            [['parttime_build_deactive'], WKDateValidator::className()],
        ];
    }
}