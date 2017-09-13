<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 13.09.2017
 * Time: 10:19
 */

namespace backend\controllers;


use yii\web\Controller;

class ConfigAuthController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}