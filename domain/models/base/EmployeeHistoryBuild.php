<?php

namespace domain\models\base;

use common\classes\validators\WKDateValidator;
use domain\forms\base\EmployeeBuildForm;
use domain\rules\base\EmployeeHistoryBuildRules;
use domain\rules\base\EmployeeHistoryRules;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use wartron\yii2uuid\behaviors\UUIDBehavior;

/**
 * This is the model class for table "{{%employee_history_build}}".
 *
 * @property integer $employee_history_id
 * @property resource $build_id
 * @property string $employee_history_build_deactive
 *
 * @property Build $build
 * @property EmployeeHistory $employeeHistory
 */
class EmployeeHistoryBuild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%employee_history_build}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(EmployeeHistoryBuildRules::client(), [
            [['build_id'], 'exist', 'skipOnError' => true, 'targetClass' => Build::className(), 'targetAttribute' => ['build_id' => 'build_id']],
            [['employee_history_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeHistory::className(), 'targetAttribute' => ['employee_history_id' => 'employee_history_id']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_history_id' => Yii::t('domain\employee_history_build', 'Employee History ID'),
            'build_id' => Yii::t('domain\employee_history_build', 'Build ID'),
            'employee_history_build_deactive' => Yii::t('domain\employee_history_build', 'Employee History Build Deactive'),
        ];
    }

    public static function create(EmployeeBuildForm $form)
    {
        return new self([
            'employee_history_id' => $form->employee_history_id,
            'build_id' => $form->build_id,
            'employee_history_build_deactive' => $form->employee_history_build_deactive,
        ]);
    }

    public function edit(EmployeeBuildForm $form)
    {
        $this->build_id = $form->build_id;
        $this->employee_history_build_deactive = $form->employee_history_build_deactive;
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
    public function getEmployeeHistory()
    {
        return $this->hasOne(EmployeeHistory::className(), ['employee_history_id' => 'employee_history_id'])->from(['employeeHistory' => EmployeeHistory::tableName()]);
    }
}
