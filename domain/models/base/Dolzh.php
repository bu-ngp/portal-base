<?php

namespace domain\models\base;

use Yii;

/**
 * This is the model class for table "{{%dolzh}}".
 *
 * @property resource $dolzh_id
 * @property string $dolzh_name
 *
 * @property Employee[] $employees
 * @property EmployeeHistory[] $employeeHistories
 * @property Parttime[] $parttimes
 */
class Dolzh extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dolzh}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dolzh_id', 'dolzh_name'], 'required'],
         //   [['dolzh_id'], 'string', 'max' => 16],
            [['dolzh_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dolzh_id' => Yii::t('domain/employee', 'Dolzh ID'),
            'dolzh_name' => Yii::t('domain/employee', 'Dolzh Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['dolzh_id' => 'dolzh_id'])->from(['employees' => Employee::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeHistories()
    {
        return $this->hasMany(EmployeeHistory::className(), ['dolzh_id' => 'dolzh_id'])->from(['employeeHistories' => EmployeeHistory::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParttimes()
    {
        return $this->hasMany(Parttime::className(), ['dolzh_id' => 'dolzh_id'])->from(['parttimes' => Parttime::tableName()]);
    }
}
