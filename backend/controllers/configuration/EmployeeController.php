<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 11:06
 */

namespace backend\controllers\configuration;


use domain\forms\base\EmployeeForm;
use domain\models\base\Dolzh;
use domain\queries\DolzhQuery;
use domain\services\base\EmployeeService;
use domain\services\proxyService;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class EmployeeController extends Controller
{
    /**
     * @var EmployeeService
     */
    private $employeeService;

    public function __construct($id, $module, EmployeeService $employeeService, $config = [])
    {
        $this->employeeService = new proxyService($employeeService);
        parent::__construct($id, $module, $config = []);
    }

    public function actionCreate()
    {
        $form = new EmployeeForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            //&& $this->employeeService->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(['configuration/users/create', 'grid' => 'EmployeesUserGrid', 'selected' => [
                'dolzh_id' => $form->dolzh_id,
                'podraz_id' => $form->podraz_id,
                'employee_begin' => $form->employee_begin,
            ]]);
        }
//
//        $form->dolzh_id = Dolzh::find()
//            ->andWhere(['like', 'dolzh_name', 'сист'])
//            ->orWhere(['like', 'dolzh_name', 'про'])
//            ->column();

//        $form->dolzh_id = Dolzh::find()
//            ->andWhere(['like', 'dolzh_name', 'сист'])
//            ->one()->dolzh_id;

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }

    public function actionTest()
    {
        return $this->render('_test');
    }

}