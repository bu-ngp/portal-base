<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 14:20
 */

namespace domain\behaviors;


use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class UpperCaseBehavior extends Behavior
{
    public $attributes = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    public function beforeValidate()
    {
        if (!empty($this->attributes)) {
            foreach ($this->attributes as $attribute) {
                $this->owner->{$attribute} = mb_strtoupper($this->owner->{$attribute}, 'UTF-8');
            }
        }
    }
}