<?php

namespace ngp\services\models\search;

use ngp\services\models\Tiles;
use domain\services\SearchModel;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class TilesSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new Tiles;
    }

    public function attributes()
    {
        return [
            'tiles_name',
            'tiles_description',
            'tiles_link',
            'tiles_keywords',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
    }

    public function defaultSortOrder()
    {
        return ['tiles_name' => SORT_ASC];
    }

    public function filter()
    {
        return [
            [['tiles_name', 'tiles_description', 'tiles_link', 'tiles_keywords', 'created_by', 'updated_by'], SearchModel::CONTAIN],
            [['created_at', 'updated_at'], SearchModel::DATETIME],
        ];
    }
}
