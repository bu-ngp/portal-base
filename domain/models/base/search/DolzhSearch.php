<?php

namespace domain\models\base\search;

use domain\models\base\Dolzh;
use domain\services\SearchModel;

class DolzhSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new Dolzh;
    }

    public function attributes()
    {
        return [
            'dolzh_name',
        ];
    }

    public function defaultSortOrder()
    {
        return ['dolzh_name' => SORT_ASC];
    }

    public function filter()
    {
        return [
            ['dolzh_name', SearchModel::CONTAIN],
        ];
    }
}
