<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:55
 */

namespace domain\models\base\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\base\Person;

/**
 * UsersSearch represents the model behind the search form about `common\models\base\Person`.
 */
class UsersSearch extends Person
{

    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), [
            'employee.dolzh.dolzh_name',
            'employee.podraz.podraz_name',
            'employee.build.build_name',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id', 'person_fullname', 'person_username', 'person_auth_key', 'person_password_hash', 'person_email', 'created_by', 'updated_by'], 'safe'],
            [['person_code', 'person_hired', 'person_fired', 'created_at', 'updated_at'], 'integer'],
            [[
                'employee.dolzh.dolzh_name',
                'employee.podraz.podraz_name',
                'employee.build.build_name',
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
        $query = Person::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith([
            'employee.dolzh',
            'employee.podraz',
            'employee.build',
        ]);

        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        // grid filtering conditions
        $query->andFilterWhere([
            'person_code' => $this->person_code,
            'person_hired' => $this->person_hired,
            'person_fired' => $this->person_fired,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'person_id', $this->person_id])
            ->andFilterWhere(['like', 'person_fullname', $this->person_fullname])
            ->andFilterWhere(['like', 'person_username', $this->person_username])
            ->andFilterWhere(['like', 'person_auth_key', $this->person_auth_key])
            ->andFilterWhere(['like', 'person_password_hash', $this->person_password_hash])
            ->andFilterWhere(['like', 'person_email', $this->person_email])
            ->andFilterWhere(['like', 'created_by', $this->created_by])
            ->andFilterWhere(['like', 'updated_by', $this->updated_by]);

        $dataProvider->sort->attributes['employee.dolzh.dolzh_name'] = [
            'asc' => ['dolzh.dolzh_name' => SORT_ASC],
            'desc' => ['dolzh.dolzh_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['employee.podraz.podraz_name'] = [
            'asc' => ['podraz.podraz_name' => SORT_ASC],
            'desc' => ['podraz.podraz_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['employee.build.build_name'] = [
            'asc' => ['build.build_name' => SORT_ASC],
            'desc' => ['build.build_name' => SORT_DESC],
        ];

        return $dataProvider;
    }
}