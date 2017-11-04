<?php

namespace doh\services\models;

use common\widgets\GridView\services\GWItemsTrait;
use Yii;

/**
 * This is the model class for table "{{%handler}}".
 *
 * @property integer $handler_id
 * @property string $identifier
 * @property string $handler_name
 * @property string $handler_description
 * @property integer $handler_at
 * @property string $handler_percent
 * @property integer $handler_status
 * @property integer $handler_done_time
 * @property string $handler_used_memory
 * @property string $handler_short_report
 * @property string $handler_files
 *
 * @property HandlerFiles[] $handlerFiles
 */
class Handler extends \yii\db\ActiveRecord
{
    use GWItemsTrait;

    const DURING = 1;
    const FINISHED = 2;
    const CANCELED = 3;
    const ERROR = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%handler}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identifier', 'handler_name', 'handler_description', 'handler_at'], 'required'],
            [['handler_at', 'handler_percent', 'handler_status', 'handler_done_time', 'handler_files'], 'integer'],
            [['identifier'], 'string', 'max' => 64],
            [['handler_name', 'handler_used_memory'], 'string', 'max' => 255],
            [['handler_description', 'handler_short_report'], 'string', 'max' => 400],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'handler_id' => Yii::t('doh', 'Handler ID'),
            'identifier' => Yii::t('doh', 'Identifier'),
            'handler_name' => Yii::t('doh', 'Handler Name'),
            'handler_description' => Yii::t('doh', 'Handler Description'),
            'handler_at' => Yii::t('doh', 'Handler At'),
            'handler_percent' => Yii::t('doh', 'Handler Percent'),
            'handler_status' => Yii::t('doh', 'Handler Status'),
            'handler_done_time' => Yii::t('doh', 'Handler Done Time'),
            'handler_used_memory' => Yii::t('doh', 'Handler Used Memory'),
            'handler_short_report' => Yii::t('doh', 'Handler Short Report'),
            'handler_files' => Yii::t('doh', 'Handler Files'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandlerFiles()
    {
        return $this->hasMany(HandlerFiles::className(), ['handler_id' => 'handler_id'])->from(['handlerFiles' => HandlerFiles::tableName()]);
    }

    public static function items()
    {
        return [
            'handler_status' => [
                self::DURING => 'В процессе',
                self::FINISHED => 'Закончен',
                self::CANCELED => 'Отменен',
                self::ERROR => 'Ошибка',
            ]
        ];
    }
}
