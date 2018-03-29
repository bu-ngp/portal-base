<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 16.06.2017
 * Time: 11:31
 */

namespace common\widgets\GridView\services;

/**
 * Трейт используется в моделях для преобразования ключей-значений в читабельные данные.
 *
 * ```php
 *      class Profile extends \yii\db\ActiveRecord
 *      {
 *          use GWItemsTrait;
 *
 *          ...
 *
 *          // Метод items() с читабельными данными для атрибута profile_sex
 *          public static function items()
 *          {
 *              return [
 *                  'profile_sex' => [
 *                      1 => 'Мужской',
 *                      2 => 'Женский',
 *                  ],
 *              ];
 *          }
 *      }
 * ```
 */
trait GWItemsTrait
{
    /**
     * Метод возвращает читабельные значения для атрибута модели.
     *
     * @param string $attribute Атрибут
     * @return array
     * @throws \Exception
     */
    public static function itemsValues($attribute)
    {
        $class = get_called_class();
        if (method_exists($class, 'items')) {
            $items = call_user_func([$class, 'items']);
            return $items[$attribute];
        } else {
            throw new \Exception('Static Method "items" not exists in class ' . get_class($class));
        }
    }
}