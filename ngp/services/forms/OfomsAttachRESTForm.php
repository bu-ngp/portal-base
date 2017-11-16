<?php

namespace ngp\services\forms;

use domain\models\base\Profile;
use domain\validators\WKDateValidator;
use ngp\services\models\Ofoms;
use ngp\services\validators\DRValidator;
use ngp\services\validators\PatientNameValidator;
use Yii;
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
            // [['dr'], WKDateValidator::className()],

            //  [['doctor'], 'exist', 'targetClass' => Profile::className(), 'targetAttribute' => 'profile_inn'],
            [['doctor'], 'match', 'pattern' => '/\d{12}/'],
            [['policy'], 'match', 'pattern' => '/\d+/'],
            [['fam', 'im', 'ot'], PatientNameValidator::className()],
            [['dr'], DRValidator::className()],
            [['policy', 'fam', 'im', 'dr', 'doctor'], 'required'],
        ];
    }

    public function getFfio()
    {
        return mb_substr($this->fam, 0, 3, 'UTF-8') . mb_substr($this->im, 0, 1, 'UTF-8') . mb_substr($this->ot, 0, 1, 'UTF-8') . mb_substr($this->dr, 2, 2, 'UTF-8');
    }
}