<?php

namespace common\widgets\ReportLoader\models;

use common\widgets\ReportLoader\BlameableBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
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
 * @property string $extension
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
            [['rl_report_filename'], 'default', 'value' => Yii::getAlias('@common') . '/tmpfiles/report' . time() . '_' . rand(1000, 9999) . '.xlsx'],
            [['rl_report_id', 'rl_report_filename', 'rl_report_displayname', 'rl_report_type'], 'required'],
            [['rl_status', 'rl_percent', 'rl_start'], 'integer'],
            [['rl_report_id'], 'unique', 'targetAttribute' => ['rl_process_id', 'rl_report_id', 'rl_status'], 'message' => Yii::t('wk-widget-report-loader', 'Report with id = "{value}" is formed')],
            [['rl_process_id', 'rl_report_id'], 'string', 'max' => 64],
            [['rl_report_filename', 'rl_report_displayname'], 'string', 'max' => 255],
            [['rl_report_type'], 'string', 'max' => 10],
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
                'createdByAttribute' => 'rl_process_id',
                'updatedByAttribute' => false,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => ['rl_process_id']
                ],
            ],
        ];
    }

    public function getExtension()
    {
        switch ($this->rl_report_type) {
            case 'Excel2007':
                return '.xlsx';
            case 'PDF':
                return '.pdf';
        }

        return false;
    }

}
