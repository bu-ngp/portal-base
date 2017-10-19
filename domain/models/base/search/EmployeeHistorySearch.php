<?php

namespace domain\models\base\search;

use domain\models\base\EmployeeHistory;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * EmployeeSearch represents the model behind the search form about `domain\models\base\Employee`.
 */
class EmployeeHistorySearch extends EmployeeHistory
{
    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'dolzh.dolzh_name',
            'podraz.podraz_name',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_history_id', 'created_at', 'updated_at'], 'integer'],
            [['person_id', 'dolzh_id', 'podraz_id', 'employee_history_begin', 'created_by', 'updated_by'], 'safe'],
            [[
                'dolzh.dolzh_name',
                'podraz.podraz_name',
            ], 'safe'],
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
        $query = EmployeeHistory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith([
            'dolzh',
            'podraz',
        ]);

        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        // grid filtering conditions
        $query->andWhere(['person_id' => Uuid::str2uuid(Yii::$app->request->get('id'))]);

        $query->andFilterWhere([
            'employee_history_id' => $this->employee_history_id,
            'employee_history_begin' => $this->employee_history_begin,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'person_id', $this->person_id])
            ->andFilterWhere(['like', 'dolzh_id', $this->dolzh_id])
            ->andFilterWhere(['like', 'podraz_id', $this->podraz_id])
            ->andFilterWhere(['like', 'created_by', $this->created_by])
            ->andFilterWhere(['like', 'updated_by', $this->updated_by]);

        $dataProvider->sort->attributes['dolzh.dolzh_name'] = [
            'asc' => ['dolzh.dolzh_name' => SORT_ASC],
            'desc' => ['dolzh.dolzh_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['podraz.podraz_name'] = [
            'asc' => ['podraz.podraz_name' => SORT_ASC],
            'desc' => ['podraz.podraz_name' => SORT_DESC],
        ];

        return $dataProvider;
    }
}
