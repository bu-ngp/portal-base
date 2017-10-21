<?php

namespace domain\models\base;

use common\classes\BlameableBehavior;
use common\models\base\Person;
use domain\forms\base\ParttimeForm;
use domain\rules\base\ParttimeRules;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%parttime}}".
 *
 * @property integer $parttime_id
 * @property resource $person_id
 * @property resource $dolzh_id
 * @property resource $podraz_id
 * @property resource $build_id
 * @property string $parttime_begin
 * @property string $parttime_end
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
class Parttime extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%parttime}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(ParttimeRules::client(), [
            [['person_id'], 'required'],
            [['dolzh_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dolzh::className(), 'targetAttribute' => ['dolzh_id' => 'dolzh_id']],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['person_id' => 'person_id']],
            [['podraz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Podraz::className(), 'targetAttribute' => ['podraz_id' => 'podraz_id']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parttime_id' => Yii::t('domain/employee', 'Parttime ID'),
            'person_id' => Yii::t('domain/employee', 'Person ID'),
            'dolzh_id' => Yii::t('domain/employee', 'Dolzh ID'),
            'podraz_id' => Yii::t('domain/employee', 'Podraz ID'),
            'build_id' => Yii::t('domain/employee', 'Build ID'),
            'parttime_begin' => Yii::t('domain/employee', 'Parttime Begin'),
            'parttime_end' => Yii::t('domain/employee', 'Parttime End'),
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
                // 'value' => new Expression('NOW()'),
                'value' => time(),
            ],
            [
                'class' => BlameableBehavior::className(),
            ],
        ];
    }

    public static function create(ParttimeForm $form)
    {
        return new self([
            'person_id' => Uuid::str2uuid($form->person_id),
            'dolzh_id' => Uuid::str2uuid($form->dolzh_id),
            'podraz_id' => Uuid::str2uuid($form->podraz_id),
            'parttime_begin' => $form->parttime_begin,
            'parttime_end' => $form->parttime_end,
        ]);
    }

    public function edit(ParttimeForm $form)
    {
        $this->dolzh_id = Uuid::str2uuid($form->dolzh_id);
        $this->podraz_id = Uuid::str2uuid($form->podraz_id);
        $this->parttime_begin = $form->parttime_begin;
        $this->parttime_end = $form->parttime_end;
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
