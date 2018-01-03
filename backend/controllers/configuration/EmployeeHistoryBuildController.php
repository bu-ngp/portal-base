<?php

namespace backend\controllers\configuration;

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\GridView\services\AjaxResponse;
use console\helpers\RbacHelper;
use domain\forms\base\EmployeeBuildForm;
use domain\forms\base\EmployeeBuildUpdateForm;
use domain\services\AjaxFilter;
use domain\services\base\EmployeeBuildService;
use domain\services\ProxyService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

class EmployeeHistoryBuildController extends Controller
{
    /**
     * @var EmployeeBuildService
     */
    private $service;

    public function __construct($id, $module, EmployeeBuildService $service, $config = [])
    {
        $this->service = new ProxyService($service);
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
        $form = new EmployeeBuildForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->service->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));
            return $this->redirect(Breadcrumbs::previousUrl());
        }

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }

    public function actionUpdate($id)
    {
        $employee = $this->service->get($id);
        $form = new EmployeeBuildUpdateForm($employee);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->service->update($id, $form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));
            return $this->redirect(Breadcrumbs::previousUrl());
        }

        return $this->render('update', [
            'modelForm' => $form,
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
