<?php

namespace common\models\base;

use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

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
class Person extends \yii\db\ActiveRecord implements IdentityInterface
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
            'person_id' => Yii::t('common/person', 'Person ID'),
            'person_code' => Yii::t('common/person', 'Person Code'),
            'person_fullname' => Yii::t('common/person', 'Person Fullname'),
            'person_username' => Yii::t('common/person', 'Person Username'),
            'person_auth_key' => Yii::t('common/person', 'Person Auth Key'),
            'person_password_hash' => Yii::t('common/person', 'Person Password Hash'),
            'person_email' => Yii::t('common/person', 'Person Email'),
            'created_at' => Yii::t('common/person', 'Created At'),
            'updated_at' => Yii::t('common/person', 'Updated At'),
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

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne(Uuid::str2uuid($id));
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->person_auth_key = Yii::$app->security->generateRandomString();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return Uuid::uuid2str($this->getPrimaryKey());
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->person_auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['person_username' => $username]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->person_password_hash);
    }
}
