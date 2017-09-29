<?php

namespace domain\models\base;

use wartron\yii2uuid\behaviors\UUIDBehavior;
use Yii;

/**
 * This is the model class for table "{{%build}}".
 *
 * @property resource $build_id
 * @property string $build_name
 *
 * @property Employee[] $employees
 * @property EmployeeHistory[] $employeeHistories
 * @property Parttime[] $parttimes
 */
class Build extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%build}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_name'], 'required'],
            [['build_id'], 'safe'],
            [['build_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'build_id' => Yii::t('domain/employee', 'Build ID'),
            'build_name' => Yii::t('domain/employee', 'Build Name'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => UUIDBehavior::className(),
                'column' => 'profile_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['build_id' => 'build_id'])->from(['employees' => Employee::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeHistories()
    {
        return $this->hasMany(EmployeeHistory::className(), ['build_id' => 'build_id'])->from(['employeeHistories' => EmployeeHistory::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParttimes()
    {
        return $this->hasMany(Parttime::className(), ['build_id' => 'build_id'])->from(['parttimes' => Parttime::tableName()]);
    }
}
