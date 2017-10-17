<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 17.10.2017
 * Time: 11:16
 */

namespace domain\rules\base;


use common\classes\validators\WKDateValidator;

class EmployeeRules
{
    public static function client()
    {
        return [
            [['dolzh_id', 'podraz_id', 'employee_begin'], 'required'],
            [['employee_begin'], WKDateValidator::className()],
        ];
    }
}