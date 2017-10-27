<?php

namespace domain\models\base\search;

use domain\helpers\BinaryHelper;
use common\widgets\GridView\services\GWItemsTrait;
use domain\services\SearchModel;
use yii\data\ActiveDataProvider;
use domain\models\base\AuthItem;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\rbac\Item;

class AuthItemUpdateSearch extends SearchModel
{
    use GWItemsTrait;

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

    public function beforeLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        $query->joinWith(['authAssignments']);
    }

    public function afterLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {
        if (BinaryHelper::isBinaryValidString($params['id'])) {
            $query->andWhere(['authAssignments.user_id' => new Expression("UNHEX('{$params['id']}')")]);
        }
        $query->andWhere(['type' => Item::TYPE_ROLE]);
    }

    public function filter()
    {
        return [
            [['name', 'description', 'ldap_group'], SearchModel::CONTAIN],
            [['created_at', 'updated_at'], SearchModel::DATETIME],
        ];
    }

    public static function items()
    {
        return static::activeRecord()->items();
    }
}
