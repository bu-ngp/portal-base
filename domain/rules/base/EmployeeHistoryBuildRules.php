<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 11:16
 */

namespace domain\rules\base;


use domain\validators\WKDateValidator;

class EmployeeHistoryBuildRules
{
    public static function client()
    {
        return [
            [['build_id'], 'required'],
            [['employee_history_id'], 'integer'],
            [['employee_history_build_deactive'], WKDateValidator::className()],
        ];
    }
}