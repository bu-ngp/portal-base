<?php

namespace backend\controllers\configuration;

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\GridView\services\AjaxResponse;
use domain\forms\base\EmployeeBuildForm;
use domain\services\base\EmployeeBuildService;
use domain\services\proxyService;
use Yii;
use yii\web\Controller;

class EmployeeHistoryBuildController extends Controller
{
    /**
     * @var EmployeeBuildService
     */
    private $service;

    public function __construct($id, $module, EmployeeBuildService $service, $config = [])
    {
        $this->service = new proxyService($service);
        parent::__construct($id, $module, $config = []);
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
        $form = new EmployeeBuildForm($employee);

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
