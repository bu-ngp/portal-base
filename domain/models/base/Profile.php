<?php

namespace domain\models\base;

use common\classes\BlameableBehavior;
use common\classes\validators\SnilsValidator;
use common\classes\validators\WKDateValidator;
use common\models\base\Person;
use domain\forms\base\ProfileForm;
use domain\services\base\dto\ProfileData;
use wartron\yii2uuid\behaviors\UUIDBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%profile}}".
 *
 * @property resource $profile_id
 * @property string $profile_inn
 * @property string $profile_dr
 * @property integer $profile_pol
 * @property string $profile_snils
 * @property string $profile_address
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property Person $person
 */
class Profile extends \yii\db\ActiveRecord
{
    const MALE = 0;
    const FEMALE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profile_id'], 'required'],
            [['profile_dr'], WKDateValidator::className()],
            [['profile_pol'], 'in', 'range' => [Profile::MALE, Profile::FEMALE]],
            [['profile_inn'], 'match', 'pattern' => '/\d{12}/', 'message' => Yii::t('domain/profile', 'INN required 12 digits')],
            [['profile_snils'], SnilsValidator::className()],
            [['profile_address'], 'string', 'max' => 400],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'profile_id' => Yii::t('domain/employee', 'Profile ID'),
            'profile_inn' => Yii::t('domain/employee', 'Profile Inn'),
            'profile_dr' => Yii::t('domain/employee', 'Profile Dr'),
            'profile_pol' => Yii::t('domain/employee', 'Profile Pol'),
            'profile_snils' => Yii::t('domain/employee', 'Profile Snils'),
            'profile_address' => Yii::t('domain/employee', 'Profile Address'),
            'created_at' => Yii::t('domain/employee', 'Created At'),
            'updated_at' => Yii::t('domain/employee', 'Updated At'),
            'created_by' => Yii::t('domain/employee', 'Created By'),
            'updated_by' => Yii::t('domain/employee', 'Updated By'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                //   'value' => new Expression('NOW()'),
                'value' => time(),
            ],
            [
                'class' => BlameableBehavior::className(),
            ],
        ];
    }

    public static function create($primaryKey, ProfileForm $profileForm)
    {
        return new self([
            'profile_id' => $primaryKey,
            'profile_inn' => $profileForm->profile_inn,
            'profile_dr' => $profileForm->profile_dr,
            'profile_pol' => $profileForm->profile_pol,
            'profile_snils' => $profileForm->profile_snils,
            'profile_address' => $profileForm->profile_address,
        ]);
    }

    public function isNotEmpty()
    {
        return $this->profile_inn
        || $this->profile_dr
        || $this->profile_pol
        || $this->profile_snils
        || $this->profile_address;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::className(), ['person_id' => 'profile_id'])->from(['person' => Person::tableName()]);
    }

    public static function items()
    {
        return [
            'profile_pol' => [
                Profile::MALE => 'Мужской',
                Profile::FEMALE => 'Женский',
            ],
        ];
    }
}
