<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 07.07.2017
 * Time: 8:45
 */

namespace common\widgets\GridView\services;

/**
 * Класс конфигурации добавления новой записи в грид при обновлении текущей записи.
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
 *              ],
 *              ...
 *          ],
 *          ...
 *      ]) ?>
 * ```
 */
class GWAddCrudConfigForUpdate
{
    protected $urlGrid;

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

        if (empty($config['urlGrid'])) {
            throw new \Exception('urlGrid variable required');
        }

        $this->urlGrid = $config['urlGrid'];
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

}