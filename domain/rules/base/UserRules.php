<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 17:03
 */

namespace domain\rules\base;


use domain\validators\FIOValidator;
use domain\validators\LoginValidator;

class UserRules
{
    public static function client()
    {
        return
            [
                [['person_fullname', 'person_username'], 'required'],
                [['person_username'], LoginValidator::className()],
                [['person_username', 'person_fullname'], 'string', 'min' => 3],
                [['person_fullname', 'person_username', 'person_email'], 'string', 'max' => 255],
                [['person_fullname'], FIOValidator::className(), 'when' => function ($model) {
                    return $model->person_code !== 1;
                }],
                [['person_email'], 'email'],
            ];
    }
}