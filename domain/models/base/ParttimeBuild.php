<?php

namespace domain\models\base;

use domain\forms\base\ParttimeBuildForm;
use domain\rules\base\ParttimeBuildRules;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

/**
 * This is the model class for table "{{%parttime_build}}".
 *
 * @property integer $pb
 * @property integer $parttime_id
 * @property resource $build_id
 * @property string $parttime_build_deactive
 *
 * @property Build $build
 * @property Parttime $parttime
 */
class ParttimeBuild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%parttime_build}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(ParttimeBuildRules::client(), [
            [['parttime_id', 'build_id'], 'unique', 'targetAttribute' => ['parttime_id', 'build_id'], 'message' => 'The combination of Parttime ID and Build ID has already been taken.'],
            [['build_id'], 'exist', 'skipOnError' => true, 'targetClass' => Build::className(), 'targetAttribute' => ['build_id' => 'build_id']],
            [['parttime_id'], 'exist', 'skipOnError' => true, 'targetClass' => Parttime::className(), 'targetAttribute' => ['parttime_id' => 'parttime_id']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pb' => Yii::t('domain\parttime_build', 'Pb'),
            'parttime_id' => Yii::t('domain\parttime_build', 'Parttime ID'),
            'build_id' => Yii::t('domain\parttime_build', 'Build ID'),
            'parttime_build_deactive' => Yii::t('domain\parttime_build', 'Parttime Build Deactive'),
        ];
    }


    public static function create(ParttimeBuildForm $form)
    {
        return new self([
            'parttime_id' => $form->parttime_id,
            'build_id' => Uuid::str2uuid($form->build_id),
            'parttime_build_deactive' => $form->parttime_build_deactive,
        ]);
    }

    public function edit(ParttimeBuildForm $form)
    {
        $this->parttime_id = $form->parttime_id;
        $this->build_id = Uuid::str2uuid($form->build_id);
        $this->parttime_build_deactive = $form->parttime_build_deactive;
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
    public function getParttime()
    {
        return $this->hasOne(Parttime::className(), ['parttime_id' => 'parttime_id'])->from(['parttime' => Parttime::tableName()]);
    }
}
