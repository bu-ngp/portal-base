<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 11:16
 */

namespace domain\rules\base;


use common\classes\validators\WKDateValidator;

class EmployeeHistoryBuildRules
{
    public static function client()
    {
        return [
            [['employee_history_id', 'build_id'], 'required'],
            [['employee_history_id'], 'integer'],
            [['employee_history_build_deactive'], WKDateValidator::className()],
        ];
    }
}