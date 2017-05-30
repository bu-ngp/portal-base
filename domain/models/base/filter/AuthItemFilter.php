<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.05.2017
 * Time: 15:58
 */

namespace domain\models\base\filter;

use Yii;
use yii\base\Model;

class AuthItemFilter extends Model
{
    public $authitem_system_roles_mark;
    public $authitem_users_roles_mark;
    public $authitem_name;

    public function rules()
    {
        return [
            [[
                'authitem_system_roles_mark',
                'authitem_users_roles_mark',
                'authitem_name',
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'authitem_system_roles_mark' => Yii::t('domain/authitem', 'System Roles Only'),
            'authitem_users_roles_mark' => Yii::t('domain/authitem', 'Users Roles Only'),
            'authitem_name' => Yii::t('domain/authitem', 'Role Name'),
        ];
    }
}