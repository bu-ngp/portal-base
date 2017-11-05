<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 10:26
 */

namespace doh\controllers;


use doh\services\classes\DoH;
use doh\services\models\search\handlerSearch;
use doh\services\TestPL;
use doh\services\TestPLError;
use Yii;
use yii\web\Controller;

class DefaultController extends Controller
{
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