<?php

namespace domain\proc\models;

use domain\proc\BlameableBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%report_loader}}".
 *
 * @property string $rl_id
 * @property string $rl_process_id
 * @property string $rl_report_id
 * @property string $rl_report_filename
 * @property string $rl_report_displayname
 * @property string $rl_report_type
 * @property integer $rl_status
 * @property integer $rl_percent
 * @property integer $rl_start
 */
class ReportLoader extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%report_loader}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rl_process_id', 'rl_report_id', 'rl_report_filename', 'rl_report_displayname', 'rl_report_type'], 'required'],
            [['rl_status', 'rl_percent', 'rl_start'], 'integer'],
            [['rl_process_id', 'rl_report_id'], 'string', 'max' => 64],
            [['rl_report_filename', 'rl_report_displayname'], 'string', 'max' => 255],
            [['rl_report_type'], 'string', 'max' => 10],
            [['rl_report_filename', 'default', 'value' => Yii::getAlias('@common') . '/tmpfiles/report' . time() . '.xlsx']],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'rl_start',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdAtAttribute' => 'rl_process_id',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /*  public function beforeSave($insert)
   {
    if ($insert) {
           $this->rl_process_id = Yii::$app->user->isGuest ? Yii::$app->session->getId() : Yii::$app->user->getId();
           $this->rl_report_filename = Yii::getAlias('@common') . '/tmpfiles/report' . time() . '.xlsx';
       }

       return parent::beforeSave($insert);
   }*/
}
