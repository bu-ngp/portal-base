<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 21:33
 */

namespace common\classes\mysql;


class Migration extends \yii\db\Migration
{
    public function blob()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_BLOB);
    }
}