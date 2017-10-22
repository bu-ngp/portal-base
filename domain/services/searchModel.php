<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 11.10.2017
 * Time: 16:47
 */

namespace domain\services;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class SearchModel extends Model
{
    /** Filter Constants */
    const STRICT = 'strict';
    const CONTAIN = 'contain';
    const CONTAIN_OF_BEGIN = 'containOfBegin';
    const CONTAIN_OF_END = 'containOfEnd';
    const DIGIT = 'digit';
    const DATE = 'date';
    const DATETIME = 'datetime';

    /** @var ActiveQuery */
    public $searchQuery;
    public $defaultSortOrder;
    /** @var ActiveDataProvider */
    protected $dataProvider;

    public function __construct($config = [])
    {
        if (!$this->searchQuery instanceof ActiveRecord) {
            throw new \RuntimeException('property $searchQuery is required and must be ActiveRecord');
        }

        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->getQuery(),
        ]);

        $sort = [];
        foreach ($this->attributes as $attribute) {
            $splited = explode('.', $attribute);
            $sqlColumn = count($splited) > 1 ? $splited[count($splited) - 2] . '.' . $splited[count($splited) - 1] : $splited[0];

            $sort[$attribute] = [
                'asc' => [$sqlColumn => SORT_ASC],
                'desc' => [$sqlColumn => SORT_DESC],
            ];
        }

        $this->dataProvider->setSort(new Sort(array_filter([
            'attributes' => $sort,
            'defaultOrder' => $this->defaultSortOrder,
        ])));

        parent::__construct($config);
    }

    public function customRules()
    {
        return [];
    }

    public function rules()
    {
        return array_merge([[$this->attributes(), 'safe']], $this->customRules());
    }

    /**
     * @return ActiveQuery
     */
    public function getQuery()
    {
        if ($this->searchQuery instanceof ActiveRecord) {
            return call_user_func([$this->searchQuery, 'find']);
        }

        throw new \RuntimeException('property $searchQuery is required');
    }

    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    public function changeSortBehavior($attribute, array $orderBehavior)
    {
        if ($this->getDataProvider()->getSort()->hasAttribute($attribute)) {
            $this->getDataProvider()->getSort()->attributes[$attribute] = $orderBehavior;
        }
    }
}