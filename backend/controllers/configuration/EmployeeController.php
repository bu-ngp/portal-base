<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 11:06
 */

namespace backend\controllers\configuration;


use common\widgets\Breadcrumbs\Breadcrumbs;
use domain\forms\base\EmployeeForm;
use domain\forms\base\EmployeeHistoryForm;
use domain\models\base\Dolzh;
use domain\models\base\EmployeeHistory;
use domain\models\base\search\BuildSearch;
use domain\models\base\search\EmployeeHistoryBuildSearch;
use domain\queries\DolzhQuery;
use domain\services\base\EmployeeHistoryService;
use domain\services\base\EmployeeService;
use domain\services\proxyService;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class EmployeeController extends Controller
{
    /**
     * @var EmployeeHistoryService
     */
    private $employeeHistoryService;

    public function __construct($id, $module, EmployeeHistoryService $employeeHistoryService, $config = [])
    {
        $this->employeeHistoryService = new proxyService($employeeHistoryService);
        parent::__construct($id, $module, $config = []);
    }

    public function actionCreate()
    {
        $form = new EmployeeHistoryForm();

//        $searchModelBuild = new BuildSearch();
//        $dataProviderBuild = $searchModelBuild->searchForEmployee(Yii::$app->request->queryParams);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $employeeId = $this->employeeHistoryService->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved. Add Builds.'));
            Breadcrumbs::removeLastCrumb();

            return $this->redirect(['update', 'id' => $employeeId]);
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
//            'searchModelBuild' => $searchModelBuild,
//            'dataProviderBuild' => $dataProviderBuild,
        ]);
    }

    public function actionUpdate($id)
    {
        $employee = $this->employeeHistoryService->get($id);
        $form = new EmployeeHistoryForm($employee);

        $searchModelEmployeeHB = new EmployeeHistoryBuildSearch();
        $dataProviderEmployeeHB = $searchModelEmployeeHB->search(Yii::$app->request->queryParams);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->employeeHistoryService->update($id, $form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(Breadcrumbs::previousUrl());
        }

        return $this->render('update', [
            'modelForm' => $form,
            'searchModelEmployeeHB' => $searchModelEmployeeHB,
            'dataProviderEmployeeHB' => $dataProviderEmployeeHB,
        ]);
    }

    public function actionTest()
    {
        return $this->render('_test');
    }

}