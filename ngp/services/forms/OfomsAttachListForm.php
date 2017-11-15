<?php

namespace ngp\services\forms;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class OfomsAttachListForm extends Model
{
    /** @var  UploadedFile */
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
        if ($this->listFile !== null) {
            $this->listFile->saveAs($this->listFile->tempName);
        }

        return parent::beforeValidate();
    }
}