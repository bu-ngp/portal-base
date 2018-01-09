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
use domain\services\base\ConfigCommonService;
use domain\services\ProxyService;
use Yii;
use yii\console\Controller;

class EmployeeController extends Controller
{
    /**
     * @var ConfigCommonService
     */
    private $service;

    public function __construct($id, $module, ConfigCommonService $service, $config = [])
    {
        $this->service = new ProxyService($service);
        parent::__construct($id, $module, $config = []);
    }

    public function actionImport()
    {
        if ($this->service->importEmployee()) {
            $filePath = Yii::getAlias('@common/ftpimport/Upload_Kamin_SotrInfo.xlsx');

            if (file_exists($filePath)) {
                $doh = new DoH(new EmployeeProccessLoader($filePath));
                $doh->execute();

                echo "finish\n";
            } else {
                echo "File '$filePath' don't exist.\n";
            }
        } else {
            echo "Import Disabled from configuration.\n";
        }
    }

    public function actionImport2()
    {
        $doh = new DoH(new EmployeeProccessLoader(Yii::getAlias('@common/ftpimport/Upload_Kamin_SotrInfo.xlsx')));
        $doh->execute();

        echo "finish\n";
    }
}