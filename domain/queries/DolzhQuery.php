<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 10.10.2017
 * Time: 8:21
 */

namespace domain\queries;


use domain\models\base\Dolzh;
use yii\db\ActiveQuery;

class DolzhQuery
{
    public static function getCallbackAllDolzhs()
    {
        return function (ActiveQuery $query) {
            return $query->select(['dolzh_id', 'dolzh_name']);
        };
    }
}