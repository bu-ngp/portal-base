<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 9:08
 */

namespace domain\behaviors;


class UUIDBehavior extends \wartron\yii2uuid\behaviors\UUIDBehavior
{
    public function events()
    {
        return[
            \yii\db\ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeCreate',
        ];
    }
}