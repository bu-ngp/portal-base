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

class RoleUpdateForm extends Model
{
    public $description;
    public $assignRoles;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'assignRoles'], 'required'],
            [['assignRoles'], 'compare', 'compareValue' => '{"checkAll":false,"included":[],"excluded":[]}', 'operator' => '!=', 'message' => Yii::t('common/roles', 'Select roles')],
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