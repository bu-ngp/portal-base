<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.06.2017
 * Time: 9:25
 */

namespace common\widgets\ReportLoader;

/**
 * Модуль настроек для виджета [[ReportLoader]].
 *
 * ```php
 *     'modules' => [
 *         ...
 *         'report-loader' => [
 *             'class' => '\common\widgets\ReportLoader\Module',
 *             'id' => 'customId',
 *         ],
 *         ...
 *     ],
 * ```
 */
class Module extends \yii\base\Module
{
    /**
     * Инициализация модуля.
     * ```php
     * $this->id = 'reportLoader';
     * ```
     */
    public function init()
    {
        $this->id = 'reportLoader';
        parent::init();
    }
}