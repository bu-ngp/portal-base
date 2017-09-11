<?php

namespace domain\models\base;

use common\models\base\Person;
use common\widgets\GridView\services\GWItemsTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * This is the model class for table "{{%auth_item}}".
 *
 * @property string $name
 * @property integer $type
 * @property integer $view
 * @property string $description
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
        return [
            [['name', 'type', 'created_at', 'updated_at'], 'required'],
            [['type', 'view', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            //    [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
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
            'rule_name' => Yii::t('domain/authitem', 'Rule Name'),
            'data' => Yii::t('domain/authitem', 'Data'),
            'created_at' => Yii::t('domain/authitem', 'Created At'),
            'updated_at' => Yii::t('domain/authitem', 'Updated At'),
        ];
    }

    public static function create($name, $description, $type)
    {
        $authItem = new self([
            'name' => $name,
            'description' => $description,
            'type' => $type
        ]);
        return $authItem;
    }

    public function rename($description)
    {
        $this->description = $description;
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

    public static function items()
    {
        return [
            'type' => [
                1 => 'Роль',
                2 => 'Разрешение',
            ],
        ];
    }
}
