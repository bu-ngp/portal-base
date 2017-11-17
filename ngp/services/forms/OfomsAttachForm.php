<?php

namespace ngp\services\forms;

use domain\models\base\Profile;
use ngp\services\models\Ofoms;
use Yii;
use yii\base\Model;

/**
 * @property string $ffio
 */
class OfomsAttachForm extends Model
{
    public $enp;
    public $fam;
    public $im;
    public $ot;
    public $dr;
    public $vrach_inn;

    public function __construct(array $config = [])
    {
        $this->load(Yii::$app->request->get(), '');
        $this->vrach_inn = Profile::findOne(['profile_inn' => $this->vrach_inn]) ? $this->vrach_inn : null;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['ot'], 'safe'],
            // [['dr'], WKDateValidator::className()],
            [['enp', 'fam', 'im', 'dr', 'vrach_inn'], 'required'],
            [['vrach_inn'], 'exist', 'targetClass' => Profile::className(), 'targetAttribute' => 'profile_inn'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge((new Ofoms)->attributeLabels(), [
            'vrach_inn' => Yii::t('ngp/ofoms', 'Vrach'),
        ]);
    }

    public function getFfio()
    {
        $fam = mb_substr($this->fam, 0, 3, 'UTF-8');
        if (($chars = 3 - mb_strlen($fam, 'UTF-8')) > 0) {
            $fam = $fam . implode('', array_pad([], $chars, ' '));
        }

        return $fam . mb_substr($this->im, 0, 1, 'UTF-8') . ($this->ot ? mb_substr($this->ot, 0, 1, 'UTF-8') : ' ') . mb_substr($this->dr, 8, 2, 'UTF-8');
    }
}