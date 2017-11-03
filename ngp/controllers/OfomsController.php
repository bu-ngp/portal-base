<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 14:37
 */

namespace ngp\controllers;

use ngp\helpers\RbacHelper;
use ngp\services\models\search\OfomsSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class OfomsController extends Controller
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
                        'actions' => ['index'],
                        'roles' => [RbacHelper::OFOMS_VIEW],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new OfomsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
          //  'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}