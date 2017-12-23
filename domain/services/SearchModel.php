<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 11.10.2017
 * Time: 16:47
 */

namespace domain\services;


use domain\validators\WKDateValidator;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\validators\NumberValidator;

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

    /** @var ActiveRecord */
    protected $activeRecord;
    /** @var ActiveDataProvider */
    protected $dataProvider;
    /** @var ActiveQuery */
    protected $query;
    private $_attributes = [];

    public function __construct($config = [])
    {
        $this->_attributes = array_combine($this->attributes(), array_pad([], count($this->attributes()), null));

        if (($this->activeRecord = static::activeRecord()) === null) {
            throw new \RuntimeException('Method activeRecord() not extended. Must be return ActiveRecord class.');
        }

        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->initQuery(),
            'pagination' => [
                'defaultPageSize' => 10,
                'pageSizeLimit' => [10, 100],
            ],
        ]);

        $this->dataProvider->setSort(new Sort(array_filter([
            'attributes' => $this->initSortAttributes(),
            'defaultOrder' => $this->defaultSortOrder(),
        ])));

        parent::__construct($config);
    }

    /**
     * @return ActiveRecord|null
     */
    public static function activeRecord()
    {
        return null;
    }

    public function customRules()
    {
        return [];
    }

    public function customAttributeLabels()
    {
        return [];
    }

    public function defaultSortOrder()
    {
        return [];
    }

    public function filter()
    {
        return [];
    }

    public function beforeLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {

    }

    public function afterLoad(ActiveQuery $query, ActiveDataProvider $dataProvider, $params)
    {

    }

    public function search($params)
    {
        $this->beforeLoad($this->getQuery(), $this->getDataProvider(), $params);
        $this->load($params);

        if (!$this->validate()) {
            return $this->getDataProvider();
        }

        $this->afterLoad($this->getQuery(), $this->getDataProvider(), $params);
        $this->applyFilters($this->filter());

        return $this->getDataProvider();
    }

    public function rules()
    {
        return array_merge([[$this->attributes(), 'safe']], $this->customRules());
    }

    public function attributeLabels()
    {
        return array_merge($this->initAttributeLabels(), $this->customAttributeLabels());
    }

    public function getQuery()
    {
        return $this->query;
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

    public static function itemsValues($attribute)
    {
        $activeRecord = static::activeRecord();
        if (method_exists($activeRecord, 'items')) {
            $items = call_user_func([$activeRecord, 'items']);
            return $items[$attribute];
        }
    }

    public function tableName()
    {
        return $this->activeRecord->tableName();
    }

    public function primaryKey()
    {
        return $this->activeRecord->primaryKey();
    }

    /**
     * @return ActiveQuery
     */
    private function initQuery()
    {
        $this->query = call_user_func([$this->activeRecord, 'find']);
        return $this->query;
    }

    private function applyFilters(array $rules)
    {
        foreach ($rules as $rule) {
            $columns = is_string($rule[0]) ? [$rule[0]] : $rule[0];
            $ruleFilter = $rule[1];

            foreach ($columns as $attribute) {
                if (!in_array($attribute, $this->attributes())) {
                    throw new \RuntimeException("attribute '$attribute' is missed");
                }

                $this->applyFilterByAttribute($attribute, $ruleFilter);
            }
        }
    }

    private function applyFilterByAttribute($attribute, $ruleFilter)
    {
        switch ($ruleFilter) {
            case SearchModel::STRICT:
                $this->getQuery()->andFilterWhere(['LIKE', $this->getSQLAttribute($attribute), $this->$attribute, false]);
                break;
            case SearchModel::CONTAIN:
                $this->getQuery()->andFilterWhere(['LIKE', $this->getSQLAttribute($attribute), $this->$attribute]);
                break;
            case SearchModel::CONTAIN_OF_BEGIN:
                $this->getQuery()->andFilterWhere(['LIKE', $this->getSQLAttribute($attribute), $this->$attribute . '%', false]);
                break;
            case SearchModel::CONTAIN_OF_END:
                $this->getQuery()->andFilterWhere(['LIKE', $this->getSQLAttribute($attribute), '%' . $this->$attribute, false]);
                break;
            case SearchModel::DIGIT:
                $this->getQuery()->andFilterWhere([$this->digitZnak($this->$attribute), $this->getSQLAttribute($attribute), $this->digitValue($this->$attribute)]);
                break;
            case SearchModel::DATE:
                $this->getQuery()->andFilterWhere($this->convertDateValueToCondition($attribute, $this[$attribute]));
                break;
            case SearchModel::DATETIME:
                $this->getQuery()->andFilterWhere($this->convertDateValueToCondition($attribute, $this->$attribute));
                break;
            default:
                throw new \RuntimeException("Invalid ruleFilter '$ruleFilter'");
        }
    }

    private function getSQLAttribute($attribute)
    {
        preg_match('/((\w+)\.)?(\w+)$/', $attribute, $matches);
        $table = call_user_func([$this->activeRecord, 'tableName']);
        $table = is_array($this->query->from) ? array_search($table, $this->query->from) : $table;
        $matches[1] = empty($matches[1]) ? "$table." : $matches[1];
        return "$matches[1]$matches[3]";
    }

    private function digitZnak($value)
    {
        preg_match('/^(<=|>=|<>|=|>|<)/', $value, $matches);
        return empty($matches[1]) ? '=' : $matches[1];
    }

    private function digitValue($value)
    {
        preg_match('/(<=|>=|<>|=|>|<)([\.\d]+)$/', $value, $matches);
        return empty($matches[2]) ? $value : $matches[2];
    }

    private function initAttributeLabels()
    {
        return $this->activeRecord->attributeLabels();
    }

    public function getAttributeLabel($attribute)
    {
        $labels = $this->attributeLabels();
        return isset($labels[$attribute]) ? $labels[$attribute] : $this->activeRecord()->getAttributeLabel($attribute);
    }

    private function initSortAttributes()
    {
        $sort = [];
        foreach ($this->attributes() as $attribute) {
            $sqlColumn = $this->getSQLAttribute($attribute);

            $sort[$attribute] = [
                'asc' => [$sqlColumn => SORT_ASC],
                'desc' => [$sqlColumn => SORT_DESC],
            ];
        }

        return $sort;
    }

    private function convertDateValueToCondition($attribute, $value)
    {
        $WKDateValidators = array_filter($this->getActiveValidatorsEx($this->activeRecord, $attribute), function ($value) {
            return $value instanceof WKDateValidator;
        });
        $NumberValidators = array_filter($this->getActiveValidatorsEx($this->activeRecord, $attribute), function ($value) {
            return $value instanceof NumberValidator;
        });

        if ($WKDateValidators) {
            return (new DateTimeCondition($this->getSQLAttribute($attribute), $value, DateTimeCondition::DATE))->convert();
        }

        if ($NumberValidators) {
            return (new DateTimeCondition($this->getSQLAttribute($attribute), $value, DateTimeCondition::INT))->convert();
        }

        return [];
    }

    private function getActiveValidatorsEx(ActiveRecord $parentModel, $attribute)
    {
        $models = explode('.', $attribute);

        if (count($models) > 1) {
            $attribute = array_pop($models);

            foreach ($models as $model) {
                $relatedClass = $parentModel->getRelation($model)->modelClass;
                $parentModel = new $relatedClass;
            }
        }

        return $parentModel->getActiveValidators($attribute);
    }

    public function __get($name)
    {
        return isset($this->_attributes[$name]) ? $this->_attributes[$name] : null;
    }

    public function __set($name, $value)
    {
        if (isset($this->_attributes[$name]) || array_key_exists($name, $this->_attributes)) {
            $this->_attributes[$name] = $value;
        }
    }
}