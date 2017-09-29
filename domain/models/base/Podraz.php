<?php

namespace domain\models\base;

use wartron\yii2uuid\behaviors\UUIDBehavior;
use Yii;

/**
 * This is the model class for table "{{%podraz}}".
 *
 * @property resource $podraz_id
 * @property string $podraz_name
 *
 * @property Employee[] $employees
 * @property EmployeeHistory[] $employeeHistories
 * @property Parttime[] $parttimes
 */
class Podraz extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%podraz}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['podraz_name'], 'required'],
            [['podraz_id'], 'safe'],
            [['podraz_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'podraz_id' => Yii::t('domain/employee', 'Podraz ID'),
            'podraz_name' => Yii::t('domain/employee', 'Podraz Name'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => UUIDBehavior::className(),
                'column' => 'podraz_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['podraz_id' => 'podraz_id'])->from(['employees' => Employee::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeHistories()
    {
        return $this->hasMany(EmployeeHistory::className(), ['podraz_id' => 'podraz_id'])->from(['employeeHistories' => EmployeeHistory::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParttimes()
    {
        return $this->hasMany(Parttime::className(), ['podraz_id' => 'podraz_id'])->from(['parttimes' => Parttime::tableName()]);
    }
}
