<?php

namespace domain\forms\base;

use domain\models\base\Podraz;
use yii\base\Model;

class PodrazForm extends Model
{
    public $podraz_name;

    public function __construct(Podraz $podraz = null, $config = [])
    {
        if ($podraz) {
            $this->load($podraz->attributes, '');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return (new Podraz())->rules();
    }

    public function attributeLabels()
    {
        return (new Podraz())->attributeLabels();
    }
}