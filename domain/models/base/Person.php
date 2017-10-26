<?php

namespace domain\models\base;

use common\classes\BlameableBehavior;
use common\classes\Ldap;
use common\classes\LdapModelInterface;
use common\classes\validators\WKDateValidator;
use domain\forms\base\UserForm;
use domain\forms\base\UserFormUpdate;
use domain\rules\base\UserRules;
use domain\validators\PersonFiredValidator;
use Exception;
use domain\behaviors\UUIDBehavior;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%person}}".
 *
 * @property resource $person_id
 * @property integer $person_code
 * @property string $person_fullname
 * @property string $person_username
 * @property string $person_auth_key
 * @property string $person_password_hash
 * @property string $person_email
 * @property integer $person_hired
 * @property integer $person_fired
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Employee $employee
 * @property EmployeeHistory[] $employeeHistories
 * @property Parttime[] $parttimes
 * @property Profile $person
 */
class Person extends \yii\db\ActiveRecord implements LdapModelInterface
{
    /**
     * @var array Группы LDAP доменного пользователя, проверяются в common\classes\WKUser
     */
    private $person_ldap_groups = [];

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
        return ArrayHelper::merge(UserRules::client(), [
            [['person_auth_key', 'person_password_hash'], 'required'],
            [['person_code', 'created_at', 'updated_at'], 'integer'],
            [['person_hired', 'person_fired'], WKDateValidator::className()],
            [['person_password_hash'], 'string', 'max' => 255],
            [['person_auth_key'], 'string', 'max' => 32],
            [['person_username'], 'unique'],
            [['person_fired'], PersonFiredValidator::className()],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'person_id' => Yii::t('domain/person', 'Person ID'),
            'person_code' => Yii::t('domain/person', 'Person Code'),
            'person_fullname' => Yii::t('domain/person', 'Person Fullname'),
            'person_username' => Yii::t('domain/person', 'Person Username'),
            'person_auth_key' => Yii::t('domain/person', 'Person Auth Key'),
            'person_password_hash' => Yii::t('domain/person', 'Person Password Hash'),
            'person_email' => Yii::t('domain/person', 'Person Email'),
            'person_hired' => Yii::t('domain/person', 'Person Hired'),
            'person_fired' => Yii::t('domain/person', 'Person Fired'),
            'created_at' => Yii::t('domain/base', 'Created At'),
            'updated_at' => Yii::t('domain/base', 'Updated At'),
            'created_by' => Yii::t('domain/base', 'Created By'),
            'updated_by' => Yii::t('domain/base', 'Updated By'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            [
                'class' => UUIDBehavior::className(),
                'column' => 'person_id',
            ],
        ];
    }

    public static function create(UserForm $userForm)
    {
        return new self([
            'person_fullname' => $userForm->person_fullname,
            'person_username' => $userForm->person_username,
            'person_auth_key' => Yii::$app->security->generateRandomString(),
            'person_password_hash' => $userForm->person_password ? Yii::$app->security->generatePasswordHash($userForm->person_password) : null,
            'person_email' => $userForm->person_email,
        ]);
    }

    public function edit(UserFormUpdate $userFormUpdate)
    {
        $this->person_fullname = $userFormUpdate->person_fullname;
        $this->person_username = $userFormUpdate->person_username;
        $this->person_email = $userFormUpdate->person_email;
        $this->person_fired = $userFormUpdate->person_fired;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'person_id'])->from(['authAssignments' => Employee::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->from(['itemNames' => AuthItem::tableName()])->viaTable('{{%auth_assignment}}', ['user_id' => 'person_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['person_id' => 'person_id'])->from(['employee' => Employee::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeHistories()
    {
        return $this->hasMany(EmployeeHistory::className(), ['person_id' => 'person_id'])->from(['employeeHistories' => EmployeeHistory::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParttimes()
    {
        return $this->hasMany(Parttime::className(), ['person_id' => 'person_id'])->from(['parttimes' => Parttime::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['profile_id' => 'person_id'])->from(['profile' => Profile::tableName()]);
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
        $user = static::findOne($id);

        if (!$user && ConfigLdap::isLdapActive()) {
            try {
                $user = Ldap::adminConnect()->find($id);
            } catch (Exception $e) {
                return null;
            }
        }

        return $user;
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
        return $this->getPrimaryKey();
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
     * @return Person|null
     */
    public static function findByUsername($username, $password)
    {
        $user = static::findOne(['person_username' => $username]);

        if ($user && Yii::$app->security->validatePassword($password, $user->person_password_hash)) {
            return $user;
        }

        if (ConfigLdap::isLdapActive()) {
            try {
                $user = Ldap::userConnect($username, $password)->findByUser($username);
            } catch (Exception $e) {
                return null;
            }
        }

        if ($user) {
            return $user;
        }

        return null;
    }

    public function isLocal()
    {
        return !$this->isNewRecord;
    }

    public function isLdap()
    {
        return $this->isNewRecord;
    }

    public function setLdapGroups(array $ldapGroups)
    {
        $this->person_ldap_groups = $ldapGroups;
    }

    public function getLdapGroups()
    {
        return $this->person_ldap_groups;
    }
}
