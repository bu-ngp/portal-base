<?php

namespace domain\models\base\search;

use domain\models\base\EmployeeHistoryBuild;
use domain\services\SearchModel;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class EmployeeHistoryBuildSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new EmployeeHistoryBuild;
    }

    public function attributes()
    {
        return [
            'employee_history_id',
            'employee_history_build_deactive',
            'build.build_name',
        ];
    }

    public function defaultSortOrder()
    {
        return ['employee_history_build_deactive' => SORT_ASC];
    }

    public function beforeLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        $query->joinWith([
            'build',
        ]);
    }

    public function afterLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        $query->andWhere(['employee_history_id' => $params['id']]);
    }

    public function filter()
    {
        return [
            [['build.build_name'], SearchModel::CONTAIN],
            [['employee_history_build_deactive'], SearchModel::DATE],
        ];
    }
}