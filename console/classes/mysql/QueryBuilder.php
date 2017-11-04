<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 15:18
 */

namespace console\classes\mysql;


class QueryBuilder extends \yii\db\mysql\QueryBuilder
{
    public function __construct($connection, $config = [])
    {
        $this->typeMap[Schema::TYPE_BASE_BINARY] = 'binary(16)';
        $this->typeMap[Schema::TYPE_BLOB] = 'blob';

        parent::__construct($connection, $config);
    }
}