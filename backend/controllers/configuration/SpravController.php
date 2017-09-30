<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.09.2017
 * Time: 12:04
 */

namespace backend\controllers\configuration;


use yii\web\Controller;

class SpravController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}