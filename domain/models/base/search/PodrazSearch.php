<?php

namespace domain\models\base\search;

use domain\models\base\Podraz;
use domain\services\SearchModel;

class PodrazSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new Podraz;
    }

    public function attributes()
    {
        return [
            'podraz_name',
        ];
    }

    public function defaultSortOrder()
    {
        return ['podraz_name' => SORT_ASC];
    }

    public function filter()
    {
        return [
            ['podraz_name', SearchModel::CONTAIN],
        ];
    }
}
