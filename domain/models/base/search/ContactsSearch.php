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

class ContactsSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new Person;
    }

    public function attributes()
    {
        return [
            'person_fullname',
            'person_email',
            'employee.dolzh.dolzh_name',
            'employee.podraz.podraz_name',
            'profile.profile_phone',
            'profile.profile_internal_phone',
        ];
    }

    public function customAttributeLabels()
    {
        return [
            'employee.dolzh.dolzh_name' => Yii::t('domain/employee', 'Dolzh ID'),
            'employee.podraz.podraz_name' => Yii::t('domain/employee', 'Podraz ID'),
            'profile.profile_phone' => Yii::t('domain/profile', 'Profile Phone'),
            'profile.profile_internal_phone' => Yii::t('domain/profile', 'Profile Internal Phone'),
        ];
    }

    public function defaultSortOrder()
    {
        return ['employee.podraz.podraz_name' => SORT_ASC, 'person_fullname' => SORT_ASC];
    }

    public function beforeLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        $query
            ->joinWith([
                'profile',
                'employee.dolzh',
                'employee.podraz',
            ])
            ->andWhere(['not', ['and',
                ['or', ['profile.profile_phone' => null], ['profile.profile_phone' => '']],
                ['or', ['profile.profile_internal_phone' => null], ['profile.profile_internal_phone' => '']],
                ['or', ['person_email' => null], ['person_email' => '']],
            ]])
            ->andWhere(['person_fired' => null])
            ->andWhere(['not', ['person_code' => 1]]);
    }

    public function filter()
    {
        return [
            [['person_fullname', 'person_email', 'employee.dolzh.dolzh_name', 'employee.podraz.podraz_name', 'profile.profile_phone', 'profile.profile_internal_phone'], SearchModel::CONTAIN],
        ];
    }
}