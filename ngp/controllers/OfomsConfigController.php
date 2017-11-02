<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 15:03
 */

namespace ngp\controllers;


use console\helpers\RbacHelper;
use yii\filters\AccessControl;
use yii\web\Controller;

class OfomsConfigController extends Controller
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
                        'roles' => [RbacHelper::ADMINISTRATOR],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {

    }
}