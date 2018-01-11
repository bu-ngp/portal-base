<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.05.2017
 * Time: 15:58
 */

namespace domain\models\base\filter;

use domain\models\base\AuthItem;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\Expression;

class AuthItemTestFilter extends Model
{
    public $dolzh_select2;
    public $authitem_type;
    public $authitem_description;
    public $authitem_users_roles_mark;
    public $authitem_update_at_period;

    public function rules()
    {
        return [
            [[
                'dolzh_select2',
                'authitem_type',
                'authitem_description',
                'authitem_users_roles_mark',
                'authitem_update_at_period',
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'dolzh_select2' => 'Select2 multiple',
            'authitem_type' => 'Select2 single',
            'authitem_description' => 'Text input',
            'authitem_users_roles_mark' => 'Mark Input',
            'authitem_update_at_period' => 'Period Input',
        ];
    }

    public function filter_dolzh_select2($modelTable, $alias)
    {
        return AuthItem::find()
            ->andWhere(["$modelTable.name" => new Expression("[[name]]")])
            ->andWhere(['LIKE', 'name', 'администратор']);
    }

    public function filter_authitem_type($modelTable, $alias)
    {
        return AuthItem::find()
            ->andWhere(["$modelTable.name" => new Expression("[[name]]")])
            ->andWhere(['type' => $this->authitem_type]);
    }

    public function filter_authitem_description($modelTable, $alias)
    {
        return AuthItem::find()
            ->andWhere(["$modelTable.name" => new Expression("[[name]]")])
            ->andWhere(['LIKE', 'description', $this->authitem_description]);
    }

    public function filter_authitem_users_roles_mark($modelTable, $alias)
    {
        return AuthItem::find()
            ->andWhere(["$modelTable.name" => new Expression("[[name]]")])
            ->andWhere(['view' => '0']);
    }

    public function filter_authitem_update_at_period($modelTable, $alias)
    {

    }
}