<?php

namespace domain\forms\base;

use common\classes\validators\SnilsValidator;
use common\classes\validators\WKDateValidator;
use domain\models\base\Profile;
use Yii;
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
        return [
            [['profile_dr'], WKDateValidator::className()],
            [['profile_pol'], 'in', 'range' => [Profile::MALE, Profile::FEMALE]],
            [['profile_inn'], 'match', 'pattern' => '/\d{12}/', 'message' => Yii::t('domain/profile', 'INN required 12 digits')],
            [['profile_snils'], SnilsValidator::className()],
            [['profile_address'], 'string', 'max' => 400],
        ];
    }

    public function attributeLabels()
    {
        return (new Profile())->attributeLabels();
    }
}