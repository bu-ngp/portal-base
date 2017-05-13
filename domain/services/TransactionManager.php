<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:23
 */

namespace domain\services;

class TransactionManager
{
    public function execute(callable $function)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            call_user_func($function);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}