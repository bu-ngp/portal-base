<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:55
 */

namespace domain\models\base\search;

use domain\services\SearchModel;
use Yii;
use yii\data\ActiveDataProvider;
use domain\models\base\Person;
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
            'profile.profile_dr',
            'profile.profile_pol',
            'profile.profile_inn',
            'profile.profile_snils',
            'profile.profile_address',
        ];
    }

    public function customAttributeLabels()
    {
        return [
            'employee.dolzh.dolzh_name' => Yii::t('domain/employee', 'Dolzh ID'),
            'employee.podraz.podraz_name' => Yii::t('domain/employee', 'Podraz ID'),
            'profile.profile_dr' => Yii::t('domain/profile', 'Profile Dr'),
            'profile.profile_pol' => Yii::t('domain/profile', 'Profile Pol'),
            'profile.profile_inn' => Yii::t('domain/profile', 'Profile Inn'),
            'profile.profile_snils' => Yii::t('domain/profile', 'Profile Snils'),
            'profile.profile_address' => Yii::t('domain/profile', 'Profile Address'),
        ];
    }

    public function defaultSortOrder()
    {
        return ['person_fullname' => SORT_ASC];
    }

    public function beforeLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        $query->joinWith([
            'profile',
            'employee.dolzh',
            'employee.podraz',
        ]);
    }

    public function filter()
    {
        return [
            ['person_code', SearchModel::DIGIT],
            [['person_fullname', 'person_username', 'person_email', 'employee.dolzh.dolzh_name', 'employee.podraz.podraz_name', 'profile.profile_address'], SearchModel::CONTAIN],
            [['person_hired', 'person_fired', 'profile.profile_dr'], SearchModel::DATE],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], SearchModel::DATETIME],
            [['profile.profile_pol', 'profile.profile_inn', 'profile.profile_snils',], SearchModel::STRICT],
        ];
    }
}