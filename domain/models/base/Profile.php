<?php

namespace domain\models\base;

use common\classes\BlameableBehavior;
use common\models\base\Person;
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
            [['profile_id'], 'safe'],
            [['profile_dr'], 'date', 'format' => 'yyyy-MM-dd'],
            [['profile_pol'], 'in', 'range' => [Profile::MALE, Profile::FEMALE]],
            [['profile_inn'], 'string', 'max' => 12],
            [['profile_snils'], 'string', 'max' => 11],
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
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
            ],
            [
                'class' => UUIDBehavior::className(),
                'column' => 'profile_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::className(), ['person_id' => 'profile_id'])->from(['person' => Person::tableName()]);
    }
}
