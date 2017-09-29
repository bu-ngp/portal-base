<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:50
 */

namespace backend\controllers\configuration;


use domain\models\base\search\UsersSearch;
use Yii;
use yii\web\Controller;

class UsersController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
       // $filterModel = new AuthItemFilter();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        //    'filterModel' => $filterModel,
        ]);
    }
}