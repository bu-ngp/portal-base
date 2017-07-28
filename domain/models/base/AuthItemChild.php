<?php

namespace domain\models\base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%auth_item_child}}".
 *
 * @property string $parent
 * @property string $child
 *
 * @property AuthItem $child0
 * @property AuthItem $parent0
 */
class AuthItemChild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item_child}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'string', 'max' => 64],
            [['child'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['child' => 'name']],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['parent' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parent' => Yii::t('domain/authitem', 'Parent'),
            'child' => Yii::t('domain/authitem', 'Child'),
        ];
    }

    public static function create(AuthItem $authParent, \stdClass $assignedKeys)
    {
        $items = [];
        $authitems = AuthItem::find()
            ->andWhere($assignedKeys->checkAll ? ['not in', 'name', $assignedKeys->excluded] : ['in', 'name', $assignedKeys->included])
            ->all();

        /** @var AuthItem $authChild */
        foreach ($authitems as $authChild) {
            $items[] = new self([
                'parent' => $authParent->name,
                'child' => $authChild->name,
            ]);
        }

        return $items;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChild0()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'child'])->from(['Child0' => AuthItem::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'parent'])->from(['Parent0' => AuthItem::tableName()]);
    }
}
