<?php

namespace app\common\models;

use Yii;

/**
 * This is the model class for table "{{%person}}".
 *
 * @property string $person_id
 * @property integer $person_code
 * @property string $person_fullname
 * @property string $person_username
 * @property string $person_auth_key
 * @property string $person_password_hash
 * @property string $person_email
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Profile $person
 */
class Person extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%person}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id', 'person_code', 'person_fullname', 'person_username', 'person_auth_key', 'person_password_hash', 'created_at', 'updated_at'], 'required'],
            [['person_code', 'created_at', 'updated_at'], 'integer'],
            [['person_id'], 'string', 'max' => 16],
            [['person_fullname', 'person_username', 'person_password_hash', 'person_email'], 'string', 'max' => 255],
            [['person_auth_key'], 'string', 'max' => 32],
            [['person_username'], 'unique'],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profile::className(), 'targetAttribute' => ['person_id' => 'profile_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'person_id' => Yii::t('app\common', 'Person ID'),
            'person_code' => Yii::t('app\common', 'Person Code'),
            'person_fullname' => Yii::t('app\common', 'Person Fullname'),
            'person_username' => Yii::t('app\common', 'Person Username'),
            'person_auth_key' => Yii::t('app\common', 'Person Auth Key'),
            'person_password_hash' => Yii::t('app\common', 'Person Password Hash'),
            'person_email' => Yii::t('app\common', 'Person Email'),
            'created_at' => Yii::t('app\common', 'Created At'),
            'updated_at' => Yii::t('app\common', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'person_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('{{%auth_assignment}}', ['user_id' => 'person_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Profile::className(), ['profile_id' => 'person_id']);
    }
}
