<?php

namespace domain\models\base\search;

use common\widgets\CardList\CardListHelper;
use common\widgets\GridView\services\GridViewHelper;
use DateTime;
use domain\models\base\filter\AuthItemFilter;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use domain\models\base\AuthItem;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * AuthItemSearch represents the model behind the search form about `domain\models\base\AuthItem`.
 */
class AuthItemSearch extends AuthItem
{
    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), [
            'authAssignments.user_id',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'ldap_group', 'rule_name', 'data', 'updated_at'], 'safe'],
            [['type', 'view', 'created_at'], 'integer'],
            [['authAssignments.user_id'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AuthItem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]],
            //   'pagination' => ['pageSize' => 6]
        ]);

        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type' => 1,
            //   'view' => 0,
            'created_at' => $this->created_at,

        ]);


        list($updated_at_begin, $updated_at_end) = explode(' - ', $this->updated_at);;

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'rule_name', $this->rule_name])
            ->andFilterWhere(['like', 'data', $this->data]);

        if ($updated_at_begin && $updated_at_end) {

            $updated_at_begin = DateTime::createFromFormat('d.m.Y', $updated_at_begin);
            $updated_at_end = DateTime::createFromFormat('d.m.Y', $updated_at_end);

            $query->andWhere(['between', 'updated_at', $updated_at_begin->format('U'), $updated_at_end->format('U')]);
        }

        CardListHelper::applyPopularityOrder($query, 'name');

        return $dataProvider;
    }

    public function searchForUsers($params)
    {
        $query = AuthItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]],
        ]);

        $this->load($params);

        $query->andFilterWhere([
            'type' => 1,
            'view' => 0,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }

    public function searchForRoles($params, $_exclude)
    {
        $query = isset($_exclude->excludeFromId)
            ? AuthItem::excludeForAuthItemChildIfUpdate($_exclude->excludeFromId)
            : AuthItem::excludeForAuthItemChildIfCreate($_exclude);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type' => 1,
            //   'view' => 0,
            'created_at' => $this->created_at,

        ]);

        list($updated_at_begin, $updated_at_end) = explode(' - ', $this->updated_at);;

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'rule_name', $this->rule_name])
            ->andFilterWhere(['like', 'data', $this->data]);

        if ($updated_at_begin && $updated_at_end) {

            $updated_at_begin = DateTime::createFromFormat('d.m.Y', $updated_at_begin);
            $updated_at_end = DateTime::createFromFormat('d.m.Y', $updated_at_end);

            $query->andWhere(['between', 'updated_at', $updated_at_begin->format('U'), $updated_at_end->format('U')]);
        }

        return $dataProvider;
    }

    public function searchForCreate($params)
    {
        $query = AuthItem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //  'pagination' => ['pageSize' => 5]
        ]);

        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type' => 1,

        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'ldap_group', $this->ldap_group, false]);

        return $dataProvider;
    }

    public function searchForUpdate($params)
    {
        $query = AuthItem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //  'pagination' => ['pageSize' => 5]
        ]);

        $this->load($params);

        $query->joinWith(['authAssignments']);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        if (GridViewHelper::isBinaryValidString($params['id'])) {
            $query->andWhere([
                'authAssignments.user_id' => new Expression("UNHEX('{$params['id']}')"),
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type' => 1,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'ldap_group', $this->ldap_group, false]);

        $a = $query->createCommand()->getRawSql();

        return $dataProvider;
    }
}
