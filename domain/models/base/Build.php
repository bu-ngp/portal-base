<?php

namespace domain\models\base;

use domain\behaviors\UpperCaseBehavior;
use domain\forms\base\BuildForm;
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
            'build_id' => Yii::t('domain/build', 'Build ID'),
            'build_name' => Yii::t('domain/build', 'Build Name'),
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

    public static function create(BuildForm $form)
    {
        return new self([
            'build_name' => $form->build_name,
        ]);
    }

    public function edit(BuildForm $form)
    {
        $this->build_name = $form->build_name;
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
    public function getEmployeeHistories()
    {
        return $this->hasMany(EmployeeHistory::className(), ['employee_history_id' => 'employee_history_id'])->viaTable('{{%employee_history_build}}', ['build_id' => 'build_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParttimeBuilds()
    {
        return $this->hasMany(ParttimeBuild::className(), ['build_id' => 'build_id'])->from(['parttimeBuilds' => ParttimeBuild::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParttimes()
    {
        return $this->hasMany(Parttime::className(), ['build_id' => 'build_id'])->from(['parttimes' => Parttime::tableName()]);
    }
}
