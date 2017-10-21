<?php

namespace domain\forms\base;

use domain\models\base\Build;
use domain\rules\base\BuildRules;
use yii\base\Model;

class BuildForm extends Model
{
    public $build_name;

    public function __construct(Build $build = null, $config = [])
    {
        if ($build) {
            $this->build_name = $build->build_name;
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return BuildRules::client();
    }

    public function attributeLabels()
    {
        return (new Build())->attributeLabels();
    }
}