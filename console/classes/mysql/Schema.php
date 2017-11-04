<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 15:08
 */

namespace console\classes\mysql;


class Schema extends \yii\db\mysql\Schema
{
    const TYPE_BLOB = 'blob';
    const TYPE_BASE_BINARY = 'basebinary';

    public $columnSchemaClass = 'console\classes\mysql\ColumnSchema';

    public function __construct($config = [])
    {
        $this->typeMap['basebinary'] = self::TYPE_BASE_BINARY;
        $this->typeMap['blob'] = self::TYPE_BLOB;

        parent::__construct($config);
    }

    public function createQueryBuilder()
    {
        return new QueryBuilder($this->db);
    }
}