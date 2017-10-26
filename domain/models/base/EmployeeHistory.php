<?php

namespace domain\models\base;

use domain\behaviors\BlameableBehavior;
use domain\validators\WKDateValidator;
use domain\forms\base\EmployeeHistoryForm;
use domain\helpers\DateHelper;
use domain\rules\base\EmployeeHistoryRules;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
 * @property Dolzh $dolzh
 * @property Person $person
 * @property Podraz $podraz
 * @property EmployeeHistoryBuild[] $employeeHistoryBuilds
 * @property Build[] $builds
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
        return array_merge(EmployeeHistoryRules::client(), [
            [['employee_history_begin'], WKDateValidator::className()],
            [['dolzh_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dolzh::className(), 'targetAttribute' => ['dolzh_id' => 'dolzh_id']],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['person_id' => 'person_id']],
            [['podraz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Podraz::className(), 'targetAttribute' => ['podraz_id' => 'podraz_id']],
            [['employee_history_begin'], 'unique', 'targetAttribute' => ['employee_history_begin']],
        ]);
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
            'saveRelations' => [
                'class'     => SaveRelationsBehavior::className(),
                'relations' => ['builds'],
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function create(EmployeeHistoryForm $form)
    {
        return new self([
            'person_id' => $form->person_id,
            'dolzh_id' => $form->dolzh_id,
            'podraz_id' => $form->podraz_id,
            'employee_history_begin' => $form->employee_history_begin,
            'builds' => $form->assignBuilds,
        ]);
    }

    public function edit(EmployeeHistoryForm $form)
    {
        $this->dolzh_id = $form->dolzh_id;
        $this->podraz_id = $form->podraz_id;
        $this->employee_history_begin = $form->employee_history_begin;
    }

    public static function activeEmployees($person_id)
    {
        return static::find()->andWhere(['person_id' => $person_id])->exists();
    }

    /**
     * @param $person_id
     * @param $date
     * @return EmployeeHistory|null|ActiveRecord
     */
    public static function denyAccessForDateFired($person_id, $date)
    {
        return static::find()
            ->andWhere(['>', 'employee_history_begin', DateHelper::rus2iso($date)])
            ->andWhere(['person_id' => $person_id])
            ->orderBy(['employee_history_begin' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    /**
     * @param $person_id
     * @return bool
     */
    public static function employeeExists($person_id)
    {
        return static::find()
            ->andWhere(['person_id' => $person_id])
            ->limit(1)
            ->exists();
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeHistoryBuilds()
    {
        return $this->hasMany(EmployeeHistoryBuild::className(), ['employee_history_id' => 'employee_history_id'])->from(['employeeHistoryBuilds' => EmployeeHistoryBuild::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilds()
    {
        return $this->hasMany(Build::className(), ['build_id' => 'build_id'])->from(['builds' => EmployeeHistoryBuild::tableName()])->viaTable('{{%employee_history_build}}', ['employee_history_id' => 'employee_history_id']);
    }
}
