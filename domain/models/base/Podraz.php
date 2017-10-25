<?php

namespace domain\models\base;

use domain\behaviors\UpperCaseBehavior;
use domain\forms\base\PodrazForm;
use domain\rules\base\PodrazRules;
use domain\behaviors\UUIDBehavior;
use Yii;
use yii\helpers\ArrayHelper;

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
        return ArrayHelper::merge(PodrazRules::client(), [
            [['podraz_name'], 'unique'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'podraz_id' => Yii::t('domain/podraz', 'Podraz ID'),
            'podraz_name' => Yii::t('domain/podraz', 'Podraz Name'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => UUIDBehavior::className(),
                'column' => 'podraz_id',
            ],
            [
                'class' => UpperCaseBehavior::className(),
                'attributes' => ['podraz_name'],
            ],
        ];
    }

    public static function create(PodrazForm $form)
    {
        return new self([
            'podraz_name' => $form->podraz_name,
        ]);
    }

    public function edit(PodrazForm $form)
    {
        $this->podraz_name = $form->podraz_name;
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
