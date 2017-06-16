<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 16.06.2017
 * Time: 11:31
 */

namespace common\widgets\GridView\services;

trait GWItemsTrait
{
    public static function itemsValues($attribute)
    {
        $class = get_called_class();
        if (method_exists($class, 'items')) {
            $items = self::items();
            return $items[$attribute];
        } else {
            throw new \Exception('Static Method "items" not exists in class ' . get_class($class));
        }
    }
}