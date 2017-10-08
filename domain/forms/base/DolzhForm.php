<?php

namespace domain\forms\base;

use domain\models\base\Dolzh;
use domain\rules\base\DolzhRules;
use yii\base\Model;

class DolzhForm extends Model
{
    public $dolzh_name;

    public function __construct(Dolzh $dolzh = null, $config = [])
    {
        if ($dolzh) {
            $this->dolzh_name = $dolzh->dolzh_name;
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return DolzhRules::client();
    }

    public function attributeLabels()
    {
        return (new Dolzh())->attributeLabels();
    }
}