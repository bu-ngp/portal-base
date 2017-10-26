<?php

namespace domain\models\base;

use domain\behaviors\BlameableBehavior;
use domain\forms\base\ParttimeForm;
use domain\rules\base\ParttimeRules;
use domain\validators\ParttimeValidator;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

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
 * @property Dolzh $dolzh
 * @property Person $person
 * @property Podraz $podraz
 * @property ParttimeBuild[] $parttimeBuilds
 * @property Build[] $builds
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
            [['dolzh_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dolzh::className(), 'targetAttribute' => ['dolzh_id' => 'dolzh_id']],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['person_id' => 'person_id']],
            [['podraz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Podraz::className(), 'targetAttribute' => ['podraz_id' => 'podraz_id']],
            [['parttime_begin', 'parttime_end'], ParttimeValidator::className()],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parttime_id' => Yii::t('domain/parttime', 'Parttime ID'),
            'person_id' => Yii::t('domain/parttime', 'Person ID'),
            'dolzh_id' => Yii::t('domain/parttime', 'Dolzh ID'),
            'podraz_id' => Yii::t('domain/parttime', 'Podraz ID'),
            'build_id' => Yii::t('domain/parttime', 'Build ID'),
            'parttime_begin' => Yii::t('domain/parttime', 'Parttime Begin'),
            'parttime_end' => Yii::t('domain/parttime', 'Parttime End'),
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

    public static function create(ParttimeForm $form)
    {
        return new self([
            'person_id' => $form->person_id,
            'dolzh_id' => $form->dolzh_id,
            'podraz_id' => $form->podraz_id,
            'parttime_begin' => $form->parttime_begin,
            'parttime_end' => $form->parttime_end,
            'builds' => $form->assignBuilds,
        ]);
    }

    public function edit(ParttimeForm $form)
    {
        $this->dolzh_id = $form->dolzh_id;
        $this->podraz_id = $form->podraz_id;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParttimeBuilds()
    {
        return $this->hasMany(ParttimeBuild::className(), ['parttime_id' => 'parttime_id'])->from(['parttimeBuilds' => ParttimeBuild::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilds()
    {
        return $this->hasMany(Build::className(), ['build_id' => 'build_id'])->from(['builds' => Podraz::tableName()])->viaTable('{{%parttime_build}}', ['parttime_id' => 'parttime_id']);
    }
}
