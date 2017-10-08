<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 17:03
 */

namespace domain\rules\base;


use common\classes\validators\WKDateValidator;
use domain\validators\FIOValidator;
use domain\validators\LoginValidator;
use Yii;

class UserRules
{
    public static function client()
    {
        return
            [
                [['person_fullname', 'person_username'], 'required'],
                [['person_fired'], WKDateValidator::className()],
                [['person_username'], LoginValidator::className()],
                [['person_username', 'person_fullname'], 'string', 'min' => 3],
                [['person_fullname', 'person_username', 'person_email'], 'string', 'max' => 255],
                [['person_fullname'], FIOValidator::className()],
                [['person_email'], 'email'],
            ];
    }
}