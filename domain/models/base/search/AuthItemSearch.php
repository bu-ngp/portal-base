<?php

namespace domain\models\base\search;

use common\widgets\CardList\CardListHelper;
use domain\services\SearchModel;
use yii\data\ActiveDataProvider;
use domain\models\base\AuthItem;
use yii\db\ActiveQuery;
use yii\rbac\Item;

class AuthItemSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new AuthItem;
    }

    public function attributes()
    {
        return [
            'name',
            'type',
            'description',
            'ldap_group',
            'created_at',
            'updated_at',
        ];
    }

    public function defaultSortOrder()
    {
        return ['description' => SORT_ASC];
    }

    public function afterLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        $query->andWhere(['type' => Item::TYPE_ROLE]);
        CardListHelper::applyPopularityOrder($query, 'name');
    }

    public function filter()
    {
        return [
            [['name', 'description', 'ldap_group'], SearchModel::CONTAIN],
            [['created_at', 'updated_at'], SearchModel::DATETIME],
        ];
    }
}
