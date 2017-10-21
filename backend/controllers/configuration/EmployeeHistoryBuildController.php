<?php

namespace backend\controllers\configuration;

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\GridView\services\AjaxResponse;
use domain\forms\base\EmployeeBuildForm;
use domain\services\base\EmployeeBuildService;
use domain\services\proxyService;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class EmployeeHistoryBuildController extends Controller
{
    /**
     * @var EmployeeBuildService
     */
    private $employeeBuildService;

    public function __construct($id, $module, EmployeeBuildService $employeeBuildService, $config = [])
    {
        $this->employeeBuildService = new proxyService($employeeBuildService);
        parent::__construct($id, $module, $config = []);
    }

    public function actionCreate()
    {
        $form = new EmployeeBuildForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $employeeId = $this->employeeBuildService->create($form)
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
        $employee = $this->employeeBuildService->get($id);
        $form = new EmployeeBuildForm($employee);

        $a=Yii::$app->request->referrer;
        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->employeeBuildService->update($id, $form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(Url::previous()/* ['configuration/users/index']*/);
        }

        return $this->render('update', [
            'modelForm' => $form,
        ]);
    }

    public function actionDelete($id)
    {
        try {
            $this->employeeBuildService->delete($id);
        } catch (\Exception $e) {
            return AjaxResponse::init(AjaxResponse::ERROR, $e->getMessage());
        }

        return AjaxResponse::init(AjaxResponse::SUCCESS);
    }
}
