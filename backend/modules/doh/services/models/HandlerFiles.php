<?php

namespace doh\services\models;

use Yii;

/**
 * This is the model class for table "{{%handler_files}}".
 *
 * @property integer $handler_files_id
 * @property integer $handler_id
 * @property integer $file_type
 * @property string $file_path
 * @property string $file_description
 *
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
            [['handler_id', 'file_type', 'file_path', 'file_description'], 'required'],
            [['handler_id', 'file_type'], 'integer'],
            [['file_path', 'file_description'], 'string', 'max' => 400],
            [['handler_id'], 'exist', 'skipOnError' => true, 'targetClass' => Handler::className(), 'targetAttribute' => ['handler_id' => 'handler_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'handler_files_id' => Yii::t('doh', 'Handler Files ID'),
            'handler_id' => Yii::t('doh', 'Handler ID'),
            'file_type' => Yii::t('doh', 'File Type'),
            'file_path' => Yii::t('doh', 'File Path'),
            'file_description' => Yii::t('doh', 'File Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandler()
    {
        return $this->hasOne(Handler::className(), ['handler_id' => 'handler_id'])->from(['handler' => Handler::tableName()]);
    }
}
