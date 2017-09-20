<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 11:17
 */

namespace domain\forms\base;

use domain\models\base\AuthItem;
use Yii;
use yii\base\Model;

class RoleForm extends Model
{
    public $name;
    public $description;
    public $ldap_group;
    public $type;
    public $assignRoles;

    private $authItem;

    public function __construct(AuthItem $authItem = null, $config = [])
    {
        $this->name = $authItem ? $authItem->name : 'UserRole' . time();
        $this->type = $authItem ? $authItem->type : 1;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'assignRoles', 'type', 'name'], 'required'],
            [['ldap_group'], 'string'],
            //    [['assignRoles'], 'compare', 'compareValue' => '[]', 'operator' => '!=', 'message' => Yii::t('common/roles', 'Need add roles')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'description' => Yii::t('common/authitem', 'Description'),
            'ldap_group' => Yii::t('domain/authitem', 'Ldap Group'),
            //  'assignRoles' => Yii::t('common/authitem', 'Assign Roles'),
        ];
    }
}