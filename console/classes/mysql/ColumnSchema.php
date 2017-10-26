<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.09.2017
 * Time: 10:54
 */

namespace console\classes\mysql;


class ColumnSchema extends \yii\db\ColumnSchema
{
    protected function typecast($value)
    {
        if ($value === '' && $this->type === Schema::TYPE_BLOB) { // иначе пустая строка переводится в null
            return $value;
        }

        return parent::typecast($value);
    }

}