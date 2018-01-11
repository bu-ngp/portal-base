<?php

namespace domain\models\base\search;

use domain\services\SearchModel;
use domain\models\base\AuthItem;

class AuthItemTestSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new AuthItem;
    }

    public function attributes()
    {
        return [
            'type',
            'view',
            'description',
            'created_at',
            'updated_at',
        ];
    }

    public function defaultSortOrder()
    {
        return ['description' => SORT_ASC];
    }

    public function filter()
    {
        return [
            [['type', 'view'], SearchModel::STRICT],
            [['description'], SearchModel::CONTAIN],
            [['created_at', 'updated_at'], SearchModel::DATETIME],
        ];
    }
}
