<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 11:16
 */

namespace domain\rules\base;


use domain\validators\WKDateValidator;

class EmployeeHistoryRules
{
    public static function client()
    {
        return [
            [['dolzh_id', 'podraz_id', 'employee_history_begin'], 'required'],
            [['employee_history_begin'], WKDateValidator::className()],
        ];
    }
}