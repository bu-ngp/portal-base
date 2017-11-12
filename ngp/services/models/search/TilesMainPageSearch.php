<?php

namespace ngp\services\models\search;

use common\widgets\CardList\CardListHelper;
use ngp\services\models\Tiles;
use domain\services\SearchModel;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class TilesMainPageSearch extends SearchModel
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
            'tiles_keywords',
            'tiles_link',
            'tiles_thumbnail',
            'tiles_icon',
            'tiles_icon_color',
        ];
    }

    public function afterLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        CardListHelper::applyPopularityOrder($query, 'tiles_id');
    }

    public function filter()
    {
        return [

        ];
    }
}
