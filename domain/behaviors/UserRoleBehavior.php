<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 10:20
 */

namespace domain\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class UserRoleBehavior extends Behavior
{
    public $nameAttribute = 'name';

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    public function beforeValidate()
    {
        if (empty($this->owner->{$this->nameAttribute}))
            $this->owner->{$this->nameAttribute} = 'UserRole' . time();
    }
}