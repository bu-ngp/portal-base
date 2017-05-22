<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 11:17
 */

namespace domain\forms\base;

use Yii;
use yii\base\Model;

class RoleForm extends Model
{
    public $name;
    public $description;
    public $type;
    public $assignRoles;

    public function __construct($config = [])
    {
        $this->name = 'UserRole' . time();
        $this->type = 1;
        $this->assignRoles = 'basePodrazEdit'/*'{"RoleFormGrid":{"checkAll":false,"included":[],"excluded":[]}}'*/;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'assignRoles', 'type', 'name'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'description' => Yii::t('common/authitem', 'Description'),
            'assignRoles' => Yii::t('common/authitem', 'Assign Roles'),
        ];
    }
}