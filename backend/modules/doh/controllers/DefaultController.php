<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 10:26
 */

namespace doh\controllers;


use doh\services\classes\DoH;
use doh\services\models\Handler;
use doh\services\models\search\handlerSearch;
use doh\services\TestPL;
use doh\services\TestPLError;
use Yii;
use yii\db\Expression;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AjaxFilter::className(),
                'only' => ['listen', 'cancel'],
            ],
            [
                'class' => ContentNegotiator::className(),
                'only' => ['listen', 'cancel'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        Yii::$app->formatter->sizeFormatBase = 1000;
        $searchModel = new HandlerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionListen($keys)
    {
        $keys = json_decode($keys);

        if ($keys === null) {
            throw new \Exception('keys JSON decode error');
        }

        if ($keys) {
            $handlers = Handler::find()
                ->select(['handler_id', new Expression('round(handler_percent / 100, 2) as handler_percent'), 'handler_status'])
                ->andWhere(['handler_id' => $keys])
                ->asArray()
                ->all();

            return $handlers ? array_map(function ($handler) {
                return [$handler['handler_id'], $handler['handler_percent'], $handler['handler_status']];
            }, $handlers) : [];
        }

        return [];
    }

    public function actionCancel($id)
    {
        if (DoH::cancel($id)) {
            return (object)['status' => 'success'];
        }
        return (object)['status' => 'error'];
    }

    public function actionTest()
    {
        $doh = new DoH(new TestPL);
        $doh->execute();
        $this->redirect('doh');
    }


    public function actionTestError()
    {
        $doh = new DoH(new TestPLError);
        $doh->execute();
        $this->redirect('doh');
    }
}