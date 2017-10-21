<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 13.09.2017
 * Time: 10:19
 */

namespace backend\controllers\configuration;


use console\helpers\RbacHelper;
use yii\filters\AccessControl;
use yii\web\Controller;

class ConfigAuthController extends Controller
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
                        'roles' => [RbacHelper::ROLE_EDIT, RbacHelper::USER_EDIT],
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