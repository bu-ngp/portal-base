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
    public $person_parttime_exist_mark;
    public $person_parttime_not_exist_mark;
    public $person_roles_exist_mark;
    public $person_roles_not_exist_mark;
    public $profile_inn_not_exist_mark;
    public $profile_snils_not_exist_mark;
    public $profile_dr_not_exist_mark;

    public function rules()
    {
        return [
            [[
                'person_active_mark',
                'person_parttime_exist_mark',
                'person_parttime_not_exist_mark',
                'person_roles_exist_mark',
                'person_roles_not_exist_mark',
                'profile_inn_not_exist_mark',
                'profile_snils_not_exist_mark',
                'profile_dr_not_exist_mark',
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'person_active_mark' => Yii::t('domain/person', 'Active Users'),
            'person_parttime_exist_mark' => Yii::t('domain/person', 'Parttime Exist'),
            'person_parttime_not_exist_mark' => Yii::t('domain/person', 'Parttime Not Exist'),
            'person_roles_exist_mark' => Yii::t('domain/person', 'Roles Exist'),
            'person_roles_not_exist_mark' => Yii::t('domain/person', 'Roles Not Exist'),
            'profile_inn_not_exist_mark' => Yii::t('domain/profile', 'INN Not Exist'),
            'profile_snils_not_exist_mark' => Yii::t('domain/profile', 'SNILS Not Exist'),
            'profile_dr_not_exist_mark' => Yii::t('domain/profile', 'Dr Not Exist'),
        ];
    }

    public function filter_person_active_mark($modelTable, $alias)
    {
        return Person::find()
            ->alias('t1')
            ->andWhere(["$modelTable.person_id" => new Expression("[[person_id]]")])
            ->andWhere(['person_fired' => null]);
    }

    public function filter_person_parttime_exist_mark($modelTable, $alias)
    {
        return Person::find()
            ->alias('t1')
            ->joinWith(['parttimes'])
            ->andWhere(["$modelTable.person_id" => new Expression("[[$alias.person_id]]")])
            ->andWhere(['not', ['parttimes.parttime_id' => null]]);
    }

    public function filter_person_parttime_not_exist_mark($modelTable, $alias)
    {
        return Person::find()
            ->alias('t1')
            ->joinWith(['parttimes'])
            ->andWhere(["$modelTable.person_id" => new Expression("[[$alias.person_id]]")])
            ->andWhere(['parttimes.parttime_id' => null]);
    }

    public function filter_person_roles_exist_mark($modelTable, $alias)
    {
        return Person::find()
            ->alias('t1')
            ->joinWith(['authAssignments'])
            ->andWhere(["$modelTable.person_id" => new Expression("[[person_id]]")])
            ->andWhere(['not', ['authAssignments.user_id' => null]]);
    }

    public function filter_person_roles_not_exist_mark($modelTable, $alias)
    {
        return Person::find()
            ->alias('t1')
            ->joinWith(['authAssignments'])
            ->andWhere(["$modelTable.person_id" => new Expression("[[person_id]]")])
            ->andWhere(['authAssignments.user_id' => null]);
    }

    public function filter_profile_inn_not_exist_mark($modelTable, $alias)
    {
        return Person::find()
            ->alias('t1')
            ->joinWith(['profile'])
            ->andWhere(["$modelTable.person_id" => new Expression("[[person_id]]")])
            ->andWhere(['or', ['profile.profile_inn' => null], 'profile.profile_inn' => '']);
    }

    public function filter_profile_snils_not_exist_mark($modelTable, $alias)
    {
        return Person::find()
            ->alias('t1')
            ->joinWith(['profile'])
            ->andWhere(["$modelTable.person_id" => new Expression("[[person_id]]")])
            ->andWhere(['or', ['profile.profile_snils' => null], 'profile.profile_snils' => '']);
    }

    public function filter_profile_dr_not_exist_mark($modelTable, $alias)
    {
        return Person::find()
            ->alias('t1')
            ->joinWith(['profile'])
            ->andWhere(["$modelTable.person_id" => new Expression("[[person_id]]")])
            ->andWhere(['or', ['profile.profile_dr' => null], 'profile.profile_dr' => '']);
    }
}