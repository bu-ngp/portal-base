<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 16.06.2017
 * Time: 17:39
 */

namespace common\widgets\GridView\services;

use common\widgets\GridView\GridView;

/**
 * Класс конфигурирования экспорта грида [[\common\widgets\GridView\GridView]].
 *
 * ```php
 *     <?= GridView::widget([
 *         ...
 *         'exportGrid' => [
 *             'enable' => true,
 *             'format' => [GridView::EXCEL, GridView::PDF],
 *             'idReportLoader' => 'wk-report-loader',
 *         ],
 *         ...
 *     ]) ?>
 * ```
 */
class GWExportGridConfiguration
{
    protected $enable = false;
    protected $format = [GridView::EXCEL, GridView::PDF];
    protected $idReportLoader;

    /**
     * Конструктор класса.
     *
     * @param array $config Конфигурация Yii2
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        if ($config['enable'] !== false && empty($config['idReportLoader'])) {
            throw new \Exception('idReportLoader required');
        }

       if (is_string($config['idReportLoader'])) {
            if (!empty($config['format']) && is_array($config['format'])) {
                $this->format = $config['format'];
            }

            if (!is_string($config['idReportLoader'])) {
                throw new \Exception('idReportLoader must be string with id ReportLoader HTML element');
            }

            $this->enable = true;
            $this->idReportLoader = $config['idReportLoader'];
        }
    }

    /**
     * Проверяет активацию экспорта грида.
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->enable;
    }

    /**
     * Возвращает массив с настроенными форматами экспорта грида. Возмдные значения (GridView::EXCEL, GridView::PDF).
     *
     * @return array
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Возвращает id атрибут HTML Виджета обработчика отчетов.
     *
     * @return string
     */
    public function getIdReportLoader()
    {
        return $this->idReportLoader;
    }
}