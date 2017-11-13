<?php

namespace ngp\services\forms;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class OfomsAttachListForm extends Model
{
    public $listFile;

    public function rules()
    {
        return [
            [['listFile'], 'required'],
            [['listFile'], 'file', 'extensions' => ['xls', 'xlsx']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'listFile' => Yii::t('ngp/ofoms', 'List File'),
        ];
    }

    public function beforeValidate()
    {
        $this->listFile = UploadedFile::getInstance($this, 'listFile');
        $this->listFile->saveAs($this->listFile->tempName);
        return parent::beforeValidate();
    }
}