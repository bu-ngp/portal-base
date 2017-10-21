<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 11:06
 */

namespace backend\controllers\configuration;


use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\GridView\services\AjaxResponse;
use console\helpers\RbacHelper;
use domain\forms\base\EmployeeHistoryForm;
use domain\models\base\search\EmployeeHistoryBuildSearch;
use domain\services\AjaxFilter;
use domain\services\base\EmployeeHistoryService;
use domain\services\proxyService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

class EmployeeController extends Controller
{
    /**
     * @var EmployeeHistoryService
     */
    private $service;

    public function __construct($id, $module, EmployeeHistoryService $service, $config = [])
    {
        $this->service = new proxyService($service);
        parent::__construct($id, $module, $config = []);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => [RbacHelper::USER_EDIT],
                    ],
                ],
            ],
            [
                'class' => AjaxFilter::className(),
                'actions' => ['delete'],
            ],
            [
                'class' => ContentNegotiator::className(),
                'only' => ['delete'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $form = new EmployeeHistoryForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $employeeId = $this->service->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved. Add Builds.'));
            Breadcrumbs::removeLastCrumb();
            return $this->redirect(['update', 'id' => $employeeId]);
        }

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }

    public function actionUpdate($id)
    {
        $employee = $this->service->get($id);
        $form = new EmployeeHistoryForm($employee);

        $searchModelEmployeeHB = new EmployeeHistoryBuildSearch();
        $dataProviderEmployeeHB = $searchModelEmployeeHB->search(Yii::$app->request->queryParams);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->service->update($employee->primaryKey, $form)
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

    public function actionDelete($id)
    {
        try {
            $this->service->delete($id);
        } catch (\Exception $e) {
            return AjaxResponse::init(AjaxResponse::ERROR, $e->getMessage());
        }

        return AjaxResponse::init(AjaxResponse::SUCCESS);
    }
}