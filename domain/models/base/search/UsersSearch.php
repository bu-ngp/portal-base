<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:55
 */

namespace domain\models\base\search;

use domain\services\SearchModel;
use yii\data\ActiveDataProvider;
use common\models\base\Person;
use yii\db\ActiveQuery;

class UsersSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new Person;
    }

    public function attributes()
    {
        return [
            'person_code',
            'person_fullname',
            'person_username',
            'person_email',
            'person_hired',
            'person_fired',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'employee.dolzh.dolzh_name',
            'employee.podraz.podraz_name',
        ];
    }

    public function customRules()
    {
        return [
            ['person_code', 'integer'],
        ];
    }

    public function defaultSortOrder()
    {
        return ['person_fullname' => SORT_ASC];
    }

    public function beforeLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        $query->joinWith([
            'employee.dolzh',
            'employee.podraz',
        ]);
    }

    public function filter()
    {
        return [
            ['person_code', SearchModel::DIGIT],
            [['person_fullname', 'person_username', 'person_email', 'employee.dolzh.dolzh_name', 'employee.podraz.podraz_name'], SearchModel::CONTAIN],
            [['person_hired', 'person_fired'], SearchModel::DATE],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], SearchModel::DATETIME],
        ];
    }
}