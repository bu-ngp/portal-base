<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 10:26
 */

namespace doh\controllers;


use console\helpers\RbacHelper;
use doh\services\classes\DoH;
use doh\services\models\DohFiles;
use doh\services\models\search\HandlerSearch;
use doh\services\TestPL;
use doh\services\TestPLError;
use doh\services\TestWithFiles;
use Yii;
use yii\filters\AccessControl;
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['test', 'test-error', 'test-with-files'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'listen', 'cancel', 'download'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'clear'],
                      //  'roles' => [RbacHelper::ADMINISTRATOR],
                    ],
                ],
            ],
            [
                'class' => AjaxFilter::className(),
                'only' => ['listen', 'cancel', 'delete', 'clear'],
            ],
            [
                'class' => ContentNegotiator::className(),
                'only' => ['listen', 'cancel', 'delete', 'clear'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionIndex()
    {
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

        return DoH::listen($keys);
    }

    public function actionCancel($id)
    {
        if (!$errorMessage = DoH::cancel($id)) {
            return (object)['result' => 'success'];
        }
        return (object)['result' => 'error', 'message' => $errorMessage];
    }

    public function actionDownload($id)
    {
        $dohFiles = DohFiles::findOne($id);
        if ($dohFiles) {
            return Yii::$app->response->sendFile($dohFiles->file_path, $dohFiles->file_description);
        }

        throw new \Exception(Yii::t('doh', "File with id = '$id' not found."));
    }

    public function actionClear()
    {
        if (!$errorMessage = DoH::clear()) {
            return (object)['result' => 'success'];
        }
        return (object)['result' => 'error', 'message' => $errorMessage];
    }

    public function actionDelete($id)
    {
        if (!$errorMessage = DoH::delete($id)) {
            return (object)['result' => 'success'];
        }
        return (object)['result' => 'error', 'message' => $errorMessage];
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

    public function actionTestWithFiles()
    {
        $doh = new DoH(new TestWithFiles);
        $doh->execute();
        $this->redirect('doh');
    }
}