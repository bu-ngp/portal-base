<?php

namespace ngp\services\models\search;

use common\widgets\CardList\CardListHelper;
use ngp\services\models\Tiles;
use domain\services\SearchModel;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class TilesMainPageSearch extends SearchModel
{
    public $search_string;

    public static function activeRecord()
    {
        return new Tiles;
    }

    public function attributes()
    {
        return ['search_string'];
    }

    public function afterLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        CardListHelper::applyPopularityOrder($query, 'tiles_id');
        $query
            ->orFilterWhere(['like', 'tiles_name', $this->search_string])
            ->orFilterWhere(['like', 'tiles_description', $this->search_string])
            ->orFilterWhere(['like', 'tiles_keywords', $this->search_string])
            ->orFilterWhere(['like', 'tiles_link', $this->search_string]);
    }
}