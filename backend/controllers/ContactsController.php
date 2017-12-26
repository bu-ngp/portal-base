<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 27.10.2017
 * Time: 13:08
 */

namespace backend\controllers;


use domain\models\base\search\ContactsSearch;
use Yii;
use yii\web\Controller;

class ContactsController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new ContactsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}