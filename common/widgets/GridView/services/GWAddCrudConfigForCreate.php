<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 07.07.2017
 * Time: 8:45
 */

namespace common\widgets\GridView\services;

/**
 * Класс конфигурации добавления новой записи в грид при создании новой записи.
 *
 * Класс используется в классе [[ActionButtonCreate]], в случае обработки кнопки `CRUD` с ключем `create`.
 *
 * ```php
 *      <?= GridView::widget([
 *          ...
 *          'crudSettings' => [
 *              ...
 *              'create' => [
 *                  'urlGrid' => 'build/index',
 *                  'inputName' => 'ModelForm[assignBuilds]',
 *              ],
 *              ...
 *          ],
 *          ...
 *      ]) ?>
 * ```
 */
class GWAddCrudConfigForCreate
{
    protected $urlGrid;
    protected $inputName;

    /**
     * Конструктор класса
     *
     * @param array $config Концигурация Yii2
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        if (!is_string($config['urlGrid']) && !is_array($config['urlGrid'])) {
            throw new \Exception('urlGrid variable must be string or array');
        }

        if (!is_string($config['inputName'])) {
            throw new \Exception('inputName variable must be string');
        }

        if (empty($config['urlGrid'])) {
            throw new \Exception('urlGrid variable required');
        }

        if (empty($config['inputName'])) {
            throw new \Exception('inputName variable required');
        }

        $this->urlGrid = $config['urlGrid'];
        $this->inputName = $config['inputName'];
    }

    /**
     * Возвращает ссылку на грид, с которого добавляется запись.
     *
     * @return array|string
     */
    public function getUrlGrid()
    {
        return $this->urlGrid;
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