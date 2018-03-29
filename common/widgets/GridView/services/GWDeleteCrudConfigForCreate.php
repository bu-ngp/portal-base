<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 07.07.2017
 * Time: 8:45
 */

namespace common\widgets\GridView\services;

/**
 * Класс конфигурации удаления записи в гриде при создании новой записи.
 *
 * Класс используется в классе [[ActionButtonDelete]], в случае обработки кнопки `CRUD` с ключем `delete`.
 *
 * ```php
 *      <?= GridView::widget([
 *          ...
 *          'crudSettings' => [
 *              ...
 *              'delete' => [
 *                  'inputName' => 'ModelForm[assignBuilds]',
 *              ],
 *              ...
 *          ],
 *          ...
 *      ]) ?>
 * ```
 */
class GWDeleteCrudConfigForCreate
{
    protected $inputName;

    /**
     * Конструктор класса
     *
     * @param array $config Концигурация Yii2
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        if (!is_string($config['inputName'])) {
            throw new \Exception('inputName variable must be string');
        }

        if (empty($config['inputName'])) {
            throw new \Exception('inputName variable required');
        }

        $this->inputName = $config['inputName'];
    }

    /**
     * Возвращает имя атрибута `name` HTML `input` элемента, хранящего первичные ключи выбранных записей.
     *
     * @return string
     */
    public function getInputName()
    {
        return $this->inputName;
    }
}