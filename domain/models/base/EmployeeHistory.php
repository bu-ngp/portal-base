<?php

namespace domain\models\base;

use common\classes\BlameableBehavior;
use common\classes\validators\WKDateValidator;
use common\models\base\Person;
use domain\forms\base\EmployeeHistoryForm;
use domain\rules\base\EmployeeHistoryRules;
use wartron\yii2uuid\helpers\Uuid;
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
        return array_merge(EmployeeHistoryRules::client(), [
            [['person_id'], 'required'],
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
                //'value' => new Expression('NOW()'),
                'value' => time(),
            ],
            [
                'class' => BlameableBehavior::className(),
            ],
        ];
    }

    public static function create(EmployeeHistoryForm $form)
    {
        return new self([
            'person_id' => Uuid::str2uuid($form->person_id),
            'dolzh_id' => Uuid::str2uuid($form->dolzh_id),
            'podraz_id' => Uuid::str2uuid($form->podraz_id),
            'employee_history_begin' => $form->employee_history_begin,
        ]);
    }

    public function edit(EmployeeHistoryForm $form)
    {
        $this->dolzh_id = Uuid::str2uuid($form->dolzh_id);
        $this->podraz_id = Uuid::str2uuid($form->podraz_id);
        $this->employee_history_begin = $form->employee_history_begin;
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
