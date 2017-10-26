<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 10.10.2017
 * Time: 8:21
 */

namespace domain\queries;


use yii\db\ActiveQuery;

class PodrazQuery
{
    public static function select()
    {
        return function (ActiveQuery $query) {
            return $query->select(['podraz_id', 'podraz_name']);
        };
    }

    public static function search()
    {
        return function (ActiveQuery $query, $searchString) {
            $query->andWhere(['like', 'podraz_name', $searchString]);
        };
    }
}