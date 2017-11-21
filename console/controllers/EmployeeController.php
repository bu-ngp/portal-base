<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 07.06.2017
 * Time: 16:50
 */

namespace console\controllers;


use doh\services\classes\DoH;
use domain\proccesses\EmployeeProccessLoader;
use Yii;
use yii\console\Controller;

class EmployeeController extends Controller
{
    public function actionImport()
    {
        $doh = new DoH(new EmployeeProccessLoader(Yii::getAlias('@common/ftpimport/Upload_Kamin_SotrInfo3.xlsx')));
        $doh->execute();

        echo "finish\n";
    }
}