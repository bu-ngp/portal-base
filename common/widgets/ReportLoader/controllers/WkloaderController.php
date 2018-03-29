<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 07.06.2017
 * Time: 16:59
 */

namespace common\widgets\ReportLoader\controllers;


use common\widgets\ReportLoader\models\ReportLoader;
use yii\console\Controller;

/**
 * Класс консольного контроллера обработчика отчетов
 */
class WkloaderController extends Controller
{
    /**
     * Действие очищает все отчеты в БД, которые старше 3 дней.
     *
     * ```bash
     * php yii wkloader/clear
     * Deleted 6 reports
     * ```
     */
    public function actionClear()
    {
        $deleted = ReportLoader::deleteAll(['<=', 'rl_start', date('Y-m-d', strtotime("-3 days"))]);
        $this->stdout("Deleted $deleted reports");
    }
}