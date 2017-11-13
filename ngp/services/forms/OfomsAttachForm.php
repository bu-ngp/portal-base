<?php

namespace ngp\services\forms;

use domain\models\base\Profile;
use domain\validators\WKDateValidator;
use ngp\services\models\Ofoms;
use Yii;
use yii\base\Model;

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
}