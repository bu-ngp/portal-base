<?php

namespace domain\models\base;

use domain\behaviors\BlameableBehavior;
use common\widgets\GridView\services\GWItemsTrait;
use domain\forms\base\ProfileForm;
use domain\rules\base\ProfileRules;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%profile}}".
 *
 * @property resource $profile_id
 * @property string $profile_inn
 * @property string $profile_dr
 * @property integer $profile_pol
 * @property string $profile_snils
 * @property string $profile_address
 * @property string $profile_phone
 * @property string $profile_internal_phone
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property Person $person
 */
class Profile extends \yii\db\ActiveRecord
{
    use GWItemsTrait;

    const MALE = 1;
    const FEMALE = 2;

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
        return array_merge(ProfileRules::client(), [
            [['profile_id'], 'required'],
            [['profile_inn', 'profile_snils'], 'unique'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'profile_id' => Yii::t('domain/profile', 'Profile ID'),
            'profile_inn' => Yii::t('domain/profile', 'Profile Inn'),
            'profile_dr' => Yii::t('domain/profile', 'Profile Dr'),
            'profile_pol' => Yii::t('domain/profile', 'Profile Pol'),
            'profile_snils' => Yii::t('domain/profile', 'Profile Snils'),
            'profile_address' => Yii::t('domain/profile', 'Profile Address'),
            'profile_phone' => Yii::t('domain/profile', 'Profile Phone'),
            'profile_internal_phone' => Yii::t('domain/profile', 'Profile Internal Phone'),
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
            'profile_phone' => $profileForm->profile_phone,
            'profile_internal_phone' => $profileForm->profile_internal_phone,
        ]);
    }

    public function edit(ProfileForm $profileForm)
    {
        $this->profile_inn = $profileForm->profile_inn;
        $this->profile_dr = $profileForm->profile_dr;
        $this->profile_pol = $profileForm->profile_pol;
        $this->profile_snils = $profileForm->profile_snils;
        $this->profile_address = $profileForm->profile_address;
        $this->profile_phone = $profileForm->profile_phone;
        $this->profile_internal_phone = $profileForm->profile_internal_phone;
    }

    public function isNotEmpty()
    {
        return $this->profile_inn
            || $this->profile_dr
            || $this->profile_pol
            || $this->profile_snils
            || $this->profile_address
            || $this->profile_phone
            || $this->profile_internal_phone;
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
