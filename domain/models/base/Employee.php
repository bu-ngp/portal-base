<?php

namespace domain\models\base;

use domain\behaviors\BlameableBehavior;
use domain\forms\base\EmployeeHistoryForm;
use domain\validators\WKDateValidator;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%employee}}".
 *
 * @property integer $employee_id
 * @property resource $person_id
 * @property resource $dolzh_id
 * @property resource $podraz_id
 * @property resource $build_id
 * @property string $employee_begin
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property Build $build
 * @property Dolzh $dolzh
 * @property Person $person
 * @property Podraz $podraz
 * @property EmployeeHistory $employeeHistory
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%employee}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id', 'dolzh_id', 'podraz_id', 'employee_begin'], 'required'],
            [['employee_begin'], WKDateValidator::className()],
            [['dolzh_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dolzh::className(), 'targetAttribute' => ['dolzh_id' => 'dolzh_id']],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['person_id' => 'person_id']],
            [['podraz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Podraz::className(), 'targetAttribute' => ['podraz_id' => 'podraz_id']],
            [['person_id'], 'unique', 'when' => function ($model) {
                return $model->isNewRecord;
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_id' => Yii::t('domain/employee', 'Employee ID'),
            'person_id' => Yii::t('domain/employee', 'Person ID'),
            'dolzh_id' => Yii::t('domain/employee', 'Dolzh ID'),
            'podraz_id' => Yii::t('domain/employee', 'Podraz ID'),
            'employee_begin' => Yii::t('domain/employee', 'Employee Begin'),
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

    public static function create(EmployeeHistory $employeeHistory)
    {
        return new self([
            'person_id' => $employeeHistory->person_id,
            'dolzh_id' => $employeeHistory->dolzh_id,
            'podraz_id' => $employeeHistory->podraz_id,
            'employee_begin' => $employeeHistory->employee_history_begin,
        ]);
    }

    public function edit(EmployeeHistory $employeeHistory)
    {
        $this->person_id = $employeeHistory->person_id;
        $this->dolzh_id = $employeeHistory->dolzh_id;
        $this->podraz_id = $employeeHistory->podraz_id;
        $this->employee_begin = $employeeHistory->employee_history_begin;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public
    function getDolzh()
    {
        return $this->hasOne(Dolzh::className(), ['dolzh_id' => 'dolzh_id'])->from(['dolzh' => Dolzh::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public
    function getPerson()
    {
        return $this->hasOne(Person::className(), ['person_id' => 'person_id'])->from(['person' => Person::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public
    function getPodraz()
    {
        return $this->hasOne(Podraz::className(), ['podraz_id' => 'podraz_id'])->from(['podraz' => Podraz::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public
    function getEmployeeHistory()
    {
        return $this->hasOne(EmployeeHistory::className(), ['person_id' => 'person_id', 'dolzh_id' => 'dolzh_id', 'podraz_id' => 'podraz_id'])->from(['employeeHistory' => EmployeeHistory::tableName()]);
    }
}