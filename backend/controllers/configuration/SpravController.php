<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.09.2017
 * Time: 12:04
 */

namespace backend\controllers\configuration;


use console\helpers\RbacHelper;
use yii\filters\AccessControl;
use yii\web\Controller;

class SpravController extends Controller
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
                        'roles' => [RbacHelper::AUTHORIZED],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}