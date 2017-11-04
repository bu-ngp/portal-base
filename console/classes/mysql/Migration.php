<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 21:33
 */

namespace console\classes\mysql;


class Migration extends \yii\db\Migration
{
    public function blob()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_BLOB);
    }

    public function baseBinary() {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_BASE_BINARY);
    }

    public function createOnlyAutoIncrement($field, $table, $after = '')
    {
        $after = $after ? "AFTER `$after`" : '';

        return $this->getDb()->createCommand("ALTER TABLE $table ADD `$field` INT(11) NOT NULL UNIQUE AUTO_INCREMENT $after")->execute();
    }
}