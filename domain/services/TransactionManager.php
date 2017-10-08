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
    public function execute(callable $function, callable $functionError = null)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            call_user_func($function);
            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            if ($functionError) {
                call_user_func($functionError, $e);
                
                return false;
            } else {
                throw $e;
            }
        }
    }
}