<?php

namespace domain\models\base\search;

use domain\models\base\Build;
use domain\services\SearchModel;

class BuildSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new Build;
    }

    public function attributes()
    {
        return [
            'build_name',
        ];
    }

    public function defaultSortOrder()
    {
        return ['build_name' => SORT_ASC];
    }

    public function filter()
    {
        return [
            ['build_name', SearchModel::CONTAIN],
        ];
    }
}
