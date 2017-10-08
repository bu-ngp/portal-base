<?php

namespace domain\forms\base;

use domain\models\base\Podraz;
use domain\rules\base\PodrazRules;
use yii\base\Model;

class PodrazForm extends Model
{
    public $podraz_name;

    public function __construct(Podraz $podraz = null, $config = [])
    {
        if ($podraz) {
            $this->podraz_name = $podraz->podraz_name;
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return PodrazRules::client();
    }

    public function attributeLabels()
    {
        return (new Podraz())->attributeLabels();
    }
}