<?php

namespace domain\forms\base;

use domain\models\base\Profile;
use yii\base\Model;

class ProfileForm extends Model
{
    public $profile_inn;
    public $profile_dr;
    public $profile_pol;
    public $profile_snils;
    public $profile_address;

    public function __construct(Profile $profile = null, $config = [])
    {
        if ($profile) {
            $this->load($profile->attributes, '');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return (new Profile())->rules();
    }

    public function attributeLabels()
    {
        return (new Profile())->attributeLabels();
    }
}