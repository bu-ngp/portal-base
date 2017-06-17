<?php

namespace common\widgets\ReportLoader\controllers;

use common\widgets\ReportLoader\models\ReportLoader;
use common\widgets\ReportLoader\ReportProcess;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 07.06.2017
 * Time: 9:12
 */
class ReportController extends Controller
{
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
                new Expression('DATE_FORMAT(rl_start,\'%d.%m.%Y %H:%i:%s\') as start'),
            ])
            ->andWhere([
                'rl_process_id' => Yii::$app->user->isGuest ? Yii::$app->session->getId() : Yii::$app->user->getId(),
            ])
            ->andWhere(['not', ['rl_status' => 3]])
            ->orderBy(['rl_id' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. Only Ajax Request Access'));
        }

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. id Variable Only Integer Access, Your Passed "{id}"', ['id' => $id]));
        }

        $ReportLoader = ReportLoader::findOne($id);

        if ($ReportLoader === false) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. Report with id = "{id}" not found in database', ['id' => $id]));
        }

        if (!file_exists($ReportLoader->rl_report_filename)) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. File of report not found on server ({file})', ['file' => $ReportLoader->rl_report_filename]));
        }

        if (!unlink($ReportLoader->rl_report_filename)) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. File remove error (({file}))', ['file' => $ReportLoader->rl_report_filename]));
        }

        return $ReportLoader->delete() === 1;
    }

    public function actionDeleteAll()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. Only Ajax Request Access'));
        }

        $ReportLoader = ReportLoader::find()->limit(1)->all();

        if ($ReportLoader) {
            array_map('unlink', glob(dirname($ReportLoader[0]->rl_report_filename) . '/*'));
            $user = Yii::$app->get('user', false);
            $session = Yii::$app->get('session', false);

            if ($user && $session) {
                $rl_process_id = $user->isGuest ? $session->id : $user->id;

                return ReportLoader::deleteAll(['rl_process_id' => $rl_process_id]) > 0;
            }
        }

        return false;
    }

    public function actionDownload($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. id Variable Only Integer Access, Your Passed "{id}"', ['id' => $id]));
        }

        $ReportLoader = ReportLoader::findOne($id);

        if ($ReportLoader === false) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. Report with id = "{id}" not found in database', ['id' => $id]));
        }

        if (!file_exists($ReportLoader->rl_report_filename)) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. File of report not found on server ({file})', ['file' => $ReportLoader->rl_report_filename]));
        }

        \Yii::$app->response->sendFile($ReportLoader->rl_report_filename, $ReportLoader->rl_report_displayname . $ReportLoader->extension, [
            'mimeType' => mime_content_type($ReportLoader->rl_report_filename),
            'inline' => true,
        ]);
    }

    public function actionCancel($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. Only Ajax Request Access'));
        }

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. id Variable Only Integer Access, Your Passed "{id}"', ['id' => $id]));
        }

        return ReportProcess::cancel($id);
    }

}