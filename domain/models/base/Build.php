<?php

namespace domain\models\base;

use domain\behaviors\UpperCaseBehavior;
use domain\rules\base\BuildRules;
use domain\behaviors\UUIDBehavior;
use Yii;
use yii\helpers\ArrayHelper;

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
        return ArrayHelper::merge(BuildRules::client(), [
            [['build_name'], 'unique'],
        ]);
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
                'column' => 'build_id',
            ],
            [
                'class' => UpperCaseBehavior::className(),
                'attributes' => ['build_name'],
            ],
        ];
    }

    public static function create($build_name)
    {
        return new self([
            'build_name' => $build_name,
        ]);
    }

    public function editData($build_name)
    {
        $this->build_name = $build_name;
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
    public function getEmployeeHistoryBuilds()
    {
        return $this->hasMany(EmployeeHistoryBuild::className(), ['build_id' => 'build_id'])->from(['employeeHistoryBuilds' => EmployeeHistoryBuild::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParttimes()
    {
        return $this->hasMany(Parttime::className(), ['build_id' => 'build_id'])->from(['parttimes' => Parttime::tableName()]);
    }
}
