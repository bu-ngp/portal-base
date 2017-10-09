<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 11:06
 */

namespace backend\controllers\configuration;


use domain\forms\base\EmployeeForm;
use domain\services\base\EmployeeService;
use Yii;
use yii\web\Controller;

class EmployeeController extends Controller
{
    /**
     * @var EmployeeService
     */
    private $employeeService;

    public function __construct($id, $module, EmployeeService $employeeService, $config = [])
    {
        $this->employeeService = $employeeService;
        parent::__construct($id, $module, $config = []);
    }

    public function actionCreate()
    {
        $form = new EmployeeForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->employeeService->create($form)
        ) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }
}