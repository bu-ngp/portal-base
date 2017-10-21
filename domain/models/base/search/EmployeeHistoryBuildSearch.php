<?php

namespace domain\models\base\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use domain\models\base\EmployeeHistoryBuild;

/**
 * EmployeeHistoryBuildSearch represents the model behind the search form about `domain\models\base\EmployeeHistoryBuild`.
 */
class EmployeeHistoryBuildSearch extends EmployeeHistoryBuild
{

    public function attributes()
    {
        return array_merge([
            'build.build_name',
        ], parent::attributes());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_history_id'], 'integer'],
            [['build_id', 'employee_history_build_deactive'], 'safe'],
            [['build.build_name'], 'safe'],
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
        $query = EmployeeHistoryBuild::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $query->andWhere(['employee_history_id' => $params['id']]);

        // grid filtering conditions
        $query->andFilterWhere([
            'employee_history_build_deactive' => $this->employee_history_build_deactive,
        ]);

        $query->andFilterWhere(['like', 'build_id', $this->build_id]);

        $dataProvider->sort->attributes['build.build_name'] = [
            'asc' => ['build.build_name' => SORT_ASC],
            'desc' => ['build.build_name' => SORT_DESC],
        ];

        return $dataProvider;
    }
}
