<?php

namespace ngp\services\models\search;

use ngp\services\models\Tiles;
use domain\services\SearchModel;

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
            'tiles_thumbnail',
            'tiles_icon',
            'tiles_icon_color',
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

        ];
    }
}
