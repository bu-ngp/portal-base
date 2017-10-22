<?php

namespace domain\models\base\search;

use domain\services\SearchModel;
use yii\data\ActiveDataProvider;
use domain\models\base\ParttimeBuild;
use yii\db\ActiveQuery;

class ParttimeBuildSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new ParttimeBuild;
    }

    public function attributes()
    {
        return [
            'parttime_id',
            'parttime_build_deactive',
            'build.build_name',
        ];
    }

    public function defaultSortOrder()
    {
        return ['parttime_build_deactive' => SORT_ASC];
    }

    public function beforeLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        $query->joinWith([
            'build',
        ]);
    }

    public function afterLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        $query->andWhere(['parttime_id' => $params['id']]);
    }

    public function filter()
    {
        return [
            [['build.build_name'], SearchModel::CONTAIN],
            [['parttime_build_deactive'], SearchModel::DATE],
        ];
    }
}
