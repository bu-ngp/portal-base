<?php

namespace domain\models\base\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use domain\models\base\Build;

/**
 * BuildSearch represents the model behind the search form about `domain\models\base\Build`.
 */
class BuildSearch extends Build
{
    public function attributes()
    {
        return array_merge([
            'employeeHistoryBuilds.employee_history_id',
        ], parent::attributes());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'build_name'], 'safe'],
            [['employeeHistoryBuilds.employee_history_id'], 'safe'],
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
        $query = Build::find();

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
        $query->andFilterWhere(['like', 'build_id', $this->build_id])
            ->andFilterWhere(['like', 'build_name', $this->build_name]);

        return $dataProvider;
    }

    public function searchForEmployee($params)
    {
        $query = Build::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $query->joinWith(['employeeHistoryBuilds']);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        $query->andWhere([
            'employeeHistoryBuilds.employee_history_id' => $params['id'],
        ]);


        // grid filtering conditions
        $query->andFilterWhere(['like', 'build_id', $this->build_id])
            ->andFilterWhere(['like', 'build_name', $this->build_name]);

        return $dataProvider;
    }
}