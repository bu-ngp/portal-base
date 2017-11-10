<?php

namespace ngp\services\forms;

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
        $this->load(Yii::$app->request->get(),'');
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['enp', 'fam', 'im', 'ot', 'dr', 'vrach_inn'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'enp' => Yii::t('ngp/ofoms', 'enp'),
            'fam' => Yii::t('ngp/ofoms', 'fam'),
            'im' => Yii::t('ngp/ofoms', 'im'),
            'ot' => Yii::t('ngp/ofoms', 'ot'),
            'dr' => Yii::t('ngp/ofoms', 'dr'),
            'vrach_inn' => Yii::t('ngp/ofoms', 'ofomsVrach'),
        ];
    }
}