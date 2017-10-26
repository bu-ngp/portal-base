<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 10.10.2017
 * Time: 8:21
 */

namespace domain\queries;


use yii\db\ActiveQuery;

class BuildQuery
{
    public static function select()
    {
        return function (ActiveQuery $query) {
            return $query->select(['build_id', 'build_name']);
        };
    }

    public static function search()
    {
        return function (ActiveQuery $query, $searchString) {
            $query->andWhere(['like', 'build_name', $searchString]);
        };
    }
}