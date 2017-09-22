<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 07.06.2017
 * Time: 16:50
 */

namespace console\controllers;


use common\widgets\ReportLoader\models\ReportLoader;
use yii\console\Controller;

class ReportloaderController extends Controller
{
    public function actionClear()
    {
        $deleted = ReportLoader::deleteAll(['<=', 'rl_start', date('Y-m-d', strtotime("-3 days"))]);

        echo "Deleted $deleted reports";
    }
}