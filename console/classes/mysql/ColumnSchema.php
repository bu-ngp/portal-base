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
        if ($value === '' && in_array($this->type,[Schema::TYPE_BLOB, Schema::TYPE_BASE_BINARY])) { // иначе пустая строка переводится в null
            return $value;
        }

        return parent::typecast($value);
    }

}