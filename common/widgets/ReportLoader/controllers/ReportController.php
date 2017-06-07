<?php

namespace common\widgets\ReportLoader\controllers;

use domain\proc\models\ReportLoader;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 07.06.2017
 * Time: 9:12
 */
class ReportController extends Controller
{
    public function actionDownload()
    {
        echo 'yes';
    }

    public function actionItems()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return ReportLoader::find()
            ->select([
                'rl_id as id',
                'rl_status as status',
                'rl_report_type as type',
                'rl_report_displayname as displayName',
                'rl_percent as percent',
                'rl_start as start',
            ])
            ->andWhere(['rl_process_id' => Yii::$app->user->isGuest ? Yii::$app->session->getId() : Yii::$app->user->getId()])
            ->asArray()
            ->all();
    }

    public function actionClear()
    {
        $deleted = ReportLoader::deleteAll([
            'rl_start' <= date('Y-m-d', strtotime("-3 days"))
        ]);
        
        echo "Deleted $deleted reports";
    }
}