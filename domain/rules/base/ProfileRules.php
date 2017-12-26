<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 17:03
 */

namespace domain\rules\base;


use domain\validators\SnilsValidator;
use domain\validators\WKDateValidator;
use domain\models\base\Profile;
use Yii;

class ProfileRules
{
    public static function client()
    {
        return
            [
                [['profile_dr'], WKDateValidator::className()],
                [['profile_pol'], 'in', 'range' => [Profile::MALE, Profile::FEMALE]],
                [['profile_inn'], 'match', 'pattern' => '/\d{12}/', 'message' => Yii::t('domain/profile', 'INN required 12 digits')],
                [['profile_snils'], SnilsValidator::className()],
                [['profile_address'], 'string', 'max' => 400],
                [['profile_phone'], 'filter', 'filter' => function ($value) {
                    return preg_replace('/[-\(\)_]/', '', $value);
                }],
                [['profile_internal_phone'], 'string', 'max' => 10],
                [['profile_internal_phone'], 'integer'],
                [['profile_phone'], 'string', 'min' => 11, 'max' => 11],
                [['profile_phone'], 'match', 'pattern' => '/^\d{11}$/'],
            ];
    }
}