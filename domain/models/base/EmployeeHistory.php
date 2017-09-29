<?php

namespace domain\models\base;

use common\classes\BlameableBehavior;
use common\models\base\Person;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%employee_history}}".
 *
 * @property integer $employee_history_id
 * @property resource $person_id
 * @property resource $dolzh_id
 * @property resource $podraz_id
 * @property resource $build_id
 * @property string $employee_history_begin
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property Build $build
 * @property Dolzh $dolzh
 * @property Person $person
 * @property Podraz $podraz
 */
class EmployeeHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%employee_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id', 'dolzh_id', 'podraz_id', 'employee_history_begin'], 'required'],
            [['employee_history_begin'], 'date', 'format' => 'yyyy-MM-dd'],
            //   [['person_id', 'dolzh_id', 'podraz_id', 'build_id'], 'string', 'max' => 16],
            [['build_id'], 'exist', 'skipOnError' => true, 'targetClass' => Build::className(), 'targetAttribute' => ['build_id' => 'build_id']],
            [['dolzh_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dolzh::className(), 'targetAttribute' => ['dolzh_id' => 'dolzh_id']],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['person_id' => 'person_id']],
            [['podraz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Podraz::className(), 'targetAttribute' => ['podraz_id' => 'podraz_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_history_id' => Yii::t('domain/employee', 'Employee History ID'),
            'person_id' => Yii::t('domain/employee', 'Person ID'),
            'dolzh_id' => Yii::t('domain/employee', 'Dolzh ID'),
            'podraz_id' => Yii::t('domain/employee', 'Podraz ID'),
            'build_id' => Yii::t('domain/employee', 'Build ID'),
            'employee_history_begin' => Yii::t('domain/employee', 'Employee History Begin'),
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Build::className(), ['build_id' => 'build_id'])->from(['build' => Build::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDolzh()
    {
        return $this->hasOne(Dolzh::className(), ['dolzh_id' => 'dolzh_id'])->from(['dolzh' => Dolzh::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::className(), ['person_id' => 'person_id'])->from(['person' => Person::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPodraz()
    {
        return $this->hasOne(Podraz::className(), ['podraz_id' => 'podraz_id'])->from(['podraz' => Podraz::tableName()]);
    }
}
