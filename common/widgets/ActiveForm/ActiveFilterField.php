<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 01.06.2017
 * Time: 18:25
 */

namespace common\widgets\ActiveForm;


class ActiveFilterField extends ActiveField
{
    
    public function checkbox($options = [], $enclosedByLabel = true)
    {
        return parent::checkbox($options, $enclosedByLabel);
    }

    public function textInput($options = [])
    {
        return parent::textInput($options);
    }
}