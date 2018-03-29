<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 16.06.2017
 * Time: 17:39
 */

namespace common\widgets\GridView\services;

use yii\base\Model;

/**
 * Класс конфигурирования дополнительного фильтра грида [[\common\widgets\GridView\GridView]].
 *
 * ```php
 *     <?= GridView::widget([
 *         ...
 *         'filterDialog' => [
 *             'enable' => true,
 *             'filterModel' => $filterModel,
 *             'filterView' => '_filter',
 *         ],
 *         ...
 *     ]) ?>
 * ```
 */
class GWFilterDialogConfiguration
{
    protected $enable = false;
    /** @var  Model|null */
    protected $filterModel;
    protected $filterView = '_filter';

    /**
     * Конструктор класса.
     *
     * @param array $config Конфигурация Yii2
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        if ($config['enable'] !== false && !($config['filterModel'] instanceof Model)) {
            throw new \Exception('filterModel() method required');
        }

        if ($config['enable'] !== false && isset($config['filterModel'])) {
            $this->enable = true;
            $this->filterModel = $config['filterModel'];
            if (is_string($config['filterView']) && !empty($config['filterView'])) {
                $this->filterView = $config['filterView'];
            }
        }
    }

    /**
     * Проверяет активацию дополнительного фильтра грида.
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->enable;
    }

    /**
     * Возвращает модель дополнительного фильтра грида.
     *
     * @return Model
     */
    public function getFilterModel()
    {
        return $this->filterModel;
    }

    /**
     * Возвращает имя представления, в котором размещена форма дополнительного фильтра грида.
     *
     * @return string
     */
    public function getFilterView()
    {
        return $this->filterView;
    }
}