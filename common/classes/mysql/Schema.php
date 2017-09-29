<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 15:08
 */

namespace common\classes\mysql;

class Schema extends \yii\db\mysql\Schema
{
    const TYPE_BLOB = 'blob';

    public function __construct($config = [])
    {
        $this->typeMap['binary'] = self::TYPE_BINARY;
        $this->typeMap['blob'] = self::TYPE_BLOB;

        parent::__construct($config);
    }

    public function createQueryBuilder()
    {
        return new QueryBuilder($this->db);
    }
}