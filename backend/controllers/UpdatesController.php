<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 27.10.2017
 * Time: 13:08
 */

namespace backend\controllers;


use yii\web\Controller;

class UpdatesController extends Controller
{
    public function actionIndex()
    {
       return $this->render('index');
    }
}