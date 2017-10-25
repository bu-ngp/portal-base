<?php

namespace domain\models\base;

use common\models\base\Person;
use Yii;

/**
 * This is the model class for table "{{%auth_assignment}}".
 *
 * @property string $user_id
 * @property string $item_name
 * @property integer $created_at
 *
 * @property Person $user
 * @property AuthItem $itemName
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_assignment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'item_name', 'created_at'], 'required'],
            [['created_at'], 'integer'],
            [['user_id'], 'string', 'max' => 16],
            [['item_name'], 'string', 'max' => 64],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['user_id' => 'person_id']],
            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['item_name' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('domain/auth-assignment', 'User ID'),
            'item_name' => Yii::t('domain/auth-assignment', 'Item Name'),
            'created_at' => Yii::t('domain/base', 'Created At'),
        ];
    }

    public static function create(Person $person, array $assignedKeys)
    {
        $items = [];
        /** @var AuthItem[] $authitems */
        $authitems = AuthItem::find()
            ->andWhere(['in', 'name', $assignedKeys])
            ->all();

        foreach ($authitems as $authitem) {
            $items[] = new self([
                'user_id' => $person->primaryKey,
                'item_name' => $authitem->name,
            ]);
        }

        return $items;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Person::className(), ['person_id' => 'user_id'])->from(['user' => Person::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name'])->from(['itemName' => AuthItem::tableName()]);
    }
}
