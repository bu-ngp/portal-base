<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 07.06.2017
 * Time: 16:59
 */

namespace common\widgets\ReportLoader\controllers;


use domain\proc\models\ReportLoader;
use yii\console\Controller;

class WkloaderController extends Controller
{
    public function actionClear()
    {
        $deleted = ReportLoader::deleteAll(['<=', 'rl_start', date('Y-m-d', strtotime("-3 days"))]);

        echo "Deleted $deleted reports";
    }
}