<?php

namespace domain\forms\base;

use domain\models\base\Profile;
use domain\rules\base\ProfileRules;
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
            $this->profile_inn = $profile->profile_inn;
            $this->profile_dr = $profile->profile_dr;
            $this->profile_pol = $profile->profile_pol;
            $this->profile_snils = $profile->profile_snils;
            $this->profile_address = $profile->profile_address;
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return ProfileRules::client();
    }

    public function attributeLabels()
    {
        return (new Profile())->attributeLabels();
    }
}