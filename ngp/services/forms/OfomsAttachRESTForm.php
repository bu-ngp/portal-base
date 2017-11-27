<?php

namespace ngp\services\forms;

use ngp\services\validators\DRValidator;
use ngp\services\validators\PatientNameValidator;
use yii\base\Model;

/**
 * @property string $ffio
 */
class OfomsAttachRESTForm extends Model
{
    public $doctor;
    public $policy;
    public $fam;
    public $im;
    public $ot;
    public $dr;

    public function rules()
    {
        return [
            [['doctor', 'policy', 'fam', 'im', 'ot', 'dr'], 'filter', 'filter' => 'trim'],
            [['doctor'], 'match', 'pattern' => '/\d{12}/'],
            [['policy'], 'match', 'pattern' => '/\d+/'],
            [['fam', 'im', 'ot'], PatientNameValidator::className()],
            [['dr'], DRValidator::className()],
            [['policy', 'fam', 'im', 'dr', 'doctor'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'policy' => 'Полис',
            'fam' => 'Фамилия',
            'im' => 'Имя',
            'ot' => 'Отчество',
            'dr' => 'Дата рождения',
            'doctor' => 'ИНН врача',
        ];
    }

    public function getFfio()
    {
        $fam = mb_substr($this->fam, 0, 3, 'UTF-8');
        if (($chars = 3 - mb_strlen($fam, 'UTF-8')) > 0) {
            $fam = $fam . implode('', array_pad([], $chars, ' '));
        }

        return $fam . mb_substr($this->im, 0, 1, 'UTF-8') . ($this->ot ? mb_substr($this->ot, 0, 1, 'UTF-8') : ' ') . mb_substr($this->dr, 2, 2, 'UTF-8');
    }
}