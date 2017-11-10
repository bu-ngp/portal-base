<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 10.10.2017
 * Time: 8:21
 */

namespace ngp\services\queries;


use yii\db\ActiveQuery;

class VrachQuery
{
    public static function select()
    {
        return function (ActiveQuery $query) {
            return $query->select(['person_id', 'person_fullname']);
        };
    }

    public static function search()
    {
        return function (ActiveQuery $query, $searchString) {
            $query->andWhere(['like', 'person_fullname', $searchString]);
        };
    }
}