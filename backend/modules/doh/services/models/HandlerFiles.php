<?php

namespace doh\services\models;

/**
 * This is the model class for table "{{%handler_files}}".
 *
 * @property integer $doh_files_id
 * @property integer $handler_id
 *
 * @property DohFiles $dohFiles
 * @property Handler $handler
 */
class HandlerFiles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%handler_files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doh_files_id', 'handler_id'], 'required'],
            [['doh_files_id', 'handler_id'], 'integer'],
            [['doh_files_id', 'handler_id'], 'unique', 'targetAttribute' => ['doh_files_id', 'handler_id'], 'message' => 'The combination of Doh Files ID and Handler ID has already been taken.'],
            [['doh_files_id'], 'exist', 'skipOnError' => true, 'targetClass' => DohFiles::className(), 'targetAttribute' => ['doh_files_id' => 'doh_files_id']],
            [['handler_id'], 'exist', 'skipOnError' => true, 'targetClass' => Handler::className(), 'targetAttribute' => ['handler_id' => 'handler_id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDohFiles()
    {
        return $this->hasOne(DohFiles::className(), ['doh_files_id' => 'doh_files_id'])->from(['dohFiles' => DohFiles::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandler()
    {
        return $this->hasOne(Handler::className(), ['handler_id' => 'handler_id'])->from(['handler' => Handler::tableName()]);
    }
}
