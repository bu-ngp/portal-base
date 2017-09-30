<?php

namespace domain\forms\base;

use domain\models\base\Dolzh;
use yii\base\Model;

class DolzhForm extends Model
{
    public $dolzh_name;

    public function __construct(Dolzh $dolzh = null, $config = [])
    {
        if ($dolzh) {
            $this->load($dolzh->attributes, '');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return (new Dolzh())->rules();
    }

    public function attributeLabels()
    {
        return (new Dolzh())->attributeLabels();
    }
}