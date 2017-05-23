<?php

namespace domain\models\base\search;

use common\widgets\CardList\CardListHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use domain\models\base\AuthItem;
use yii\data\ArrayDataProvider;
use yii\db\Expression;

/**
 * AuthItemSearch represents the model behind the search form about `domain\models\base\AuthItem`.
 */
class AuthItemSearch extends AuthItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'rule_name', 'data'], 'safe'],
            [['type', 'view', 'created_at', 'updated_at'], 'integer'],
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

        if (!Yii::$app->request->isAjax) {
            return new ArrayDataProvider();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 3]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //   'type' => 1,
            //   'view' => 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'rule_name', $this->rule_name])
            ->andFilterWhere(['like', 'data', $this->data]);

        CardListHelper::applyPopularityOrder($query, 'name');

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

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type' => 1,

        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andWhere('1=2');

        return $dataProvider;
    }
}
