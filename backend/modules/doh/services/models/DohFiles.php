<?php

namespace doh\services\models;

use rmrevin\yii\fontawesome\FA;
use Yii;

/**
 * This is the model class for table "{{%doh_files}}".
 *
 * @property integer $doh_files_id
 * @property integer $file_type
 * @property string $file_path
 * @property string $file_description
 *
 * @property HandlerFiles[] $handlerFiles
 * @property Handler[] $handlers
 */
class DohFiles extends \yii\db\ActiveRecord
{
    const FILE_PDF = 'pdf';
    const FILE_EXCEL = 'excel';
    const FILE_DOC = 'word';
    const FILE_CSV = 'csv';
    const FILE_TXT = 'txt';
    const FILE_XML = 'xml';
    const FILE_ZIP = 'zip';
    const FILE_UNKNOWN = 'unknown';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%doh_files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_type', 'file_path', 'file_description'], 'required'],
            [['file_path', 'file_description'], 'string', 'max' => 400],
            [['file_type'], 'string', 'max' => 255],
         ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'doh_files_id' => Yii::t('doh', 'Doh Files ID'),
            'file_type' => Yii::t('doh', 'File Type'),
            'file_path' => Yii::t('doh', 'File Path'),
            'file_description' => Yii::t('doh', 'File Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandlerFiles()
    {
        return $this->hasMany(HandlerFiles::className(), ['doh_files_id' => 'doh_files_id'])->from(['handlerFiles' => HandlerFiles::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandlers()
    {
        return $this->hasMany(Handler::className(), ['handler_id' => 'handler_id'])->from(['handlers' => Handler::tableName()])->viaTable('{{%handler_files}}', ['doh_files_id' => 'doh_files_id']);
    }

    public static function faFileType($file_type) {
        switch ($file_type) {
            case DohFiles::FILE_PDF:
                return FA::_FILE_PDF_O;
                break;
            case DohFiles::FILE_EXCEL:
                return FA::_FILE_EXCEL_O;
                break;
            case DohFiles::FILE_DOC:
                return FA::_FILE_WORD_O;
                break;
            case DohFiles::FILE_CSV:
                return FA::_TABLE;
                break;
            case DohFiles::FILE_TXT:
                return FA::_FILE_TEXT_O;
                break;
            case DohFiles::FILE_XML:
                return FA::_FILE_CODE_O;
                break;
            case DohFiles::FILE_ZIP:
                return FA::_FILE_ZIP_O;
                break;
            default:
                return FA::_FILE;
        }
    }
}
