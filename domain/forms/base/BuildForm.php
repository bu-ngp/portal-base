<?php

namespace domain\forms\base;

use domain\models\base\Build;
use yii\base\Model;

class BuildForm extends Model
{
    public $build_name;

    public function __construct(Build $build = null, $config = [])
    {
        if ($build) {
            $this->load($build->attributes, '');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return (new Build())->rules();
    }

    public function attributeLabels()
    {
        return (new Build())->attributeLabels();
    }
}