<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 07.06.2017
 * Time: 9:12
 */

namespace common\widgets\ReportLoader\controllers;

use common\widgets\ReportLoader\models\ReportLoader;
use common\widgets\ReportLoader\ReportProcess;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Веб контроллер обработчика отчетов
 */
class ReportController extends Controller
{
    /**
     * Возвращает набор поведений подключенных к контроллеру.
     *
     * ```php
     * return [
     *             [
     *                  'class' => 'yii\filters\AjaxFilter',
     *                  'only' => ['delete', 'cancel'],
     *             ],
     *        ];
     * ```
     */
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\AjaxFilter',
                'only' => ['delete', 'cancel'],
            ],
        ];
    }

    /**
     * Действие возвращает `json` массив с набором данных о выполненных или обрабатываемых отчетов.
     *
     * **Пример выполнения:**
     *
     * ```json
     *     [
     *         {
     *             "id":"8",
     *             "status":"2",
     *             "type":"PDF",
     *             "displayName":"Отчет",
     *             "percent":"100",
     *             "start":"29.03.2018 12:02:42"
     *         },
     *         {
     *             "id":"7",
     *             "status":"2",
     *             "type":"PDF",
     *             "displayName":"Отчет",
     *             "percent":"100",
     *             "start":"29.03.2018 10:50:05"
     *         }
     *     ]
     * ```
     *
     * @return string
     */
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
                'rl_process_id' => Yii::$app->user->isGuest ? Yii::$app->session->getId() : Uuid::uuid2str(Yii::$app->user->getId()),
            ])
            ->andWhere(['not', ['rl_status' => ReportLoader::CANCEL]])
            ->orderBy(['rl_id' => SORT_DESC])
            ->asArray()
            ->all();
    }

    /**
     * Действие удаляет обработчик отчета с указанным инкрементным идентификатором у текущего пользователя.
     *
     * @param int $id Инкрементный идентификатор обработчика отчета.
     * @return string
     * @throws HttpException
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. id Variable Only Integer Access, Your Passed "{id}"', ['id' => $id]));
        }

        $user = Yii::$app->get('user', false);
        $session = Yii::$app->get('session', false);

        if ($user && $session) {
            $rl_process_id = $user->isGuest ? $session->id : Uuid::uuid2str($user->id);
            /** @var ReportLoader $ReportLoader */
            $ReportLoader = ReportLoader::find()->andWhere(['rl_id' => $id, 'rl_process_id' => $rl_process_id])->one();

            if (empty($ReportLoader)) {
                throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. Report with id = "{id}" not found in database by current user or session', ['id' => $id]));
            }

            if (!file_exists($ReportLoader->rl_report_filename)) {
                throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. File of report not found on server ({file})', ['file' => $ReportLoader->rl_report_filename]));
            }

            if (!unlink($ReportLoader->rl_report_filename)) {
                throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. File remove error (({file}))', ['file' => $ReportLoader->rl_report_filename]));
            }

            return $ReportLoader->delete() === 1;
        }

        return false;
    }

    /**
     * Действие очищает все отчеты у текущего пользователя.
     *
     * @return bool
     * @throws HttpException
     */
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
                $rl_process_id = $user->isGuest ? $session->id : Uuid::uuid2str($user->id);

                return ReportLoader::deleteAll(['rl_process_id' => $rl_process_id]) > 0;
            }
        }

        return false;
    }

    /** Действие отправляет файл отчета текущему пользователю пользователю с указанным инкрементным идентификатором обработчика отчета.
     *
     * @param int $id Инкрементный идентификатор обработчика отчета.
     * @throws HttpException
     */
    public function actionDownload($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. id Variable Only Integer Access, Your Passed "{id}"', ['id' => $id]));
        }

        $user = Yii::$app->get('user', false);
        $session = Yii::$app->get('session', false);
        if ($user && $session) {
            $rl_process_id = $user->isGuest ? $session->id : Uuid::uuid2str($user->id);
            /** @var ReportLoader $ReportLoader */
            $ReportLoader = ReportLoader::find()->andWhere([
                'rl_id' => $id,
                'rl_process_id' => $rl_process_id,
                'rl_status' => ReportLoader::COMPLETE,
            ])->one();

            if (empty($ReportLoader)) {
                throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. Report with id = "{id}" not found in database', ['id' => $id]));
            }

            if (!file_exists($ReportLoader->rl_report_filename)) {
                throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. File of report not found on server ({file})', ['file' => $ReportLoader->rl_report_filename]));
            }

            \Yii::$app->response->sendFile($ReportLoader->rl_report_filename, $ReportLoader->rl_report_displayname . $ReportLoader->extension, ['inline' => true]);
        }
    }

    /**
     * Действие отменяет выполнение отчета с указанным инкрементным идентификатором для текущего пользователя.
     *
     * @param int $id Инкрементный идентификатор обработчика отчета.
     * @return bool
     * @throws HttpException
     */
    public function actionCancel($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new HttpException(500, Yii::t('wk-widget-report-loader', 'Error. id Variable Only Integer Access, Your Passed "{id}"', ['id' => $id]));
        }

        $user = Yii::$app->get('user', false);
        $session = Yii::$app->get('session', false);

        if ($user && $session) {
            $rl_process_id = $user->isGuest ? $session->id : Uuid::uuid2str($user->id);
            /** @var ReportLoader $ReportLoader */
            $ReportLoader = ReportLoader::find()->andWhere([
                'rl_id' => $id,
                'rl_process_id' => $rl_process_id,
                'rl_status' => ReportLoader::PROGRESS,
            ])->one();

            if ($ReportLoader) {
                return ReportProcess::cancel($id);
            }
        }

        return false;
    }
}