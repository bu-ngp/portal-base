<?php

namespace domain\models\base;

use common\widgets\GridView\services\GWItemsTrait;
use domain\behaviors\UserRoleBehavior;
use domain\forms\base\RoleForm;
use domain\forms\base\RoleUpdateForm;
use domain\rules\base\RoleRules;
use domain\validators\WKDateValidator;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

/**
 * This is the model class for table "{{%auth_item}}".
 *
 * @property string $name
 * @property integer $type
 * @property integer $view
 * @property string $description
 * @property string $ldap_group
 * @property string $rule_name
 * @property resource $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property Person[] $users
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property AuthItem[] $parents
 * @property AuthItem[] $children
 */
class AuthItem extends \yii\db\ActiveRecord
{
    use GWItemsTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(RoleRules::client(), [
            [['name', 'type'], 'required'],
            [['created_at', 'updated_at', 'type', 'view'], 'integer'], // 'created_at', 'updated_at': для работы фильтра грида
            [['data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name', 'description'], 'unique'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('domain/authitem', 'Name'),
            'type' => Yii::t('domain/authitem', 'Type'),
            'view' => Yii::t('domain/authitem', 'View'),
            'description' => Yii::t('domain/authitem', 'Description'),
            'ldap_group' => Yii::t('domain/authitem', 'Ldap Group'),
            'rule_name' => Yii::t('domain/authitem', 'Rule Name'),
            'data' => Yii::t('domain/authitem', 'Data'),
            'created_at' => Yii::t('domain/base', 'Created At'),
            'updated_at' => Yii::t('domain/base', 'Updated At'),
        ];
    }

    public function behaviors()
    {
        return [
            UserRoleBehavior::className(),
        ];
    }

    public static function create(RoleForm $form)
    {
        return new self([
            'description' => $form->description,
            'ldap_group' => $form->ldap_group,
            'type' => Item::TYPE_ROLE,
        ]);
    }

    public function edit(RoleUpdateForm $form)
    {
        $this->description = $form->description;
        $this->ldap_group = $form->ldap_group;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name'])->from(['authAssignments' => AuthAssignment::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Person::className(), ['person_id' => 'user_id'])->viaTable('{{%auth_assignment}}', ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name'])->from(['authItemChildren' => AuthItemChild::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name'])->from(['authItemChildren0' => AuthItemChild::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('{{%auth_item_child}}', ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('{{%auth_item_child}}', ['parent' => 'name']);
    }

    public static function funcExcludeForRoles()
    {
        return function (ActiveQuery $activeQuery, array $ids) {
            $activeQuery
                ->andWhere(['not in', 'name', $ids])
                ->andWhere(['not exists', (new Query())
                    ->select('{{%auth_item_child}}.child')
                    ->from('{{%auth_item_child}}')
                    ->andWhere(['in', '{{%auth_item_child}}.parent', $ids])
                    ->andWhere('{{%auth_item_child}}.child = {{%auth_item}}.name')
                ]);
        };
    }

    public static function funcExcludeForUsers()
    {
        return function (ActiveQuery $activeQuery, array $ids) {
            $activeQuery
                ->andWhere(['not in', 'name', $ids])
                ->andWhere(['not exists', (new Query())
                    ->select('{{%auth_assignment}}.item_name')
                    ->from('{{%auth_assignment}}')
                    ->andWhere(['in', '{{%auth_assignment}}.user_id', $ids])
                    ->andWhere('{{%auth_assignment}}.item_name = {{%auth_item}}.name')
                ]);
        };
    }

    public static function items()
    {
        return [
            'type' => [
                Item::TYPE_ROLE => 'Роль',
                Item::TYPE_PERMISSION => 'Разрешение',
            ],
            'view' => [
                0 => 'Пользовательская',
                1 => 'Системная',
            ],
        ];
    }
}
