<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 11:17
 */

namespace domain\forms\base;

use domain\models\base\AuthItem;
use domain\rules\base\RoleRules;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class RoleForm extends Model
{
    public $name;
    public $description;
    public $ldap_group;
    public $type;
    public $assignRoles;

    /**
     * @inheritdoc
     */
    public function rules()
    {
       // return [];
        return ArrayHelper::merge(RoleRules::client(), [
            [['assignRoles'], 'required'],
        ]);
    }

    public function attributeLabels()
    {
        return (new AuthItem())->attributeLabels();
    }
}