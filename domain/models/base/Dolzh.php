<?php

namespace domain\models\base;

use domain\behaviors\UpperCaseBehavior;
use domain\rules\base\DolzhRules;
use domain\behaviors\UUIDBehavior;
use Yii;
use yii\helpers\ArrayHelper;

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
        return ArrayHelper::merge(DolzhRules::client(), [
            [['dolzh_name'], 'unique'],
        ]);
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

    public function behaviors()
    {
        return [
            [
                'class' => UUIDBehavior::className(),
                'column' => 'dolzh_id',
            ],
            [
                'class' => UpperCaseBehavior::className(),
                'attributes' => ['dolzh_name'],
            ],
        ];
    }

    public static function create($dolzh_name)
    {
        return new self([
            'dolzh_name' => $dolzh_name,
        ]);
    }

    public function editData($dolzh_name)
    {
        $this->dolzh_name = $dolzh_name;
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
