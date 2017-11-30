<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.05.2017
 * Time: 15:58
 */

namespace domain\models\base\filter;

use domain\models\base\Person;
use Yii;
use yii\base\Model;
use yii\db\Expression;

class UsersFilter extends Model
{
    public $person_active_mark;

    public function rules()
    {
        return [
            [[
                'person_active_mark',
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'person_active_mark' => Yii::t('domain/person', 'Active Users'),
        ];
    }

    public function filter_person_active_mark($modelTable, $alias)
    {
        return Person::find()
            ->alias('t1')
            ->andWhere(["$modelTable.person_id" => new Expression("[[person_id]]")])
            ->andWhere(['person_fired' => null]);
    }
}