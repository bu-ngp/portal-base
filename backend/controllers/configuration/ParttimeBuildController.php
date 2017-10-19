<?php

namespace backend\controllers\configuration;

use common\widgets\GridView\services\AjaxResponse;
use domain\forms\base\ParttimeBuildForm;
use domain\services\base\ParttimeBuildService;
use domain\services\proxyService;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class ParttimeBuildController extends Controller
{
    /**
     * @var ParttimeBuildService
     */
    private $parttimeBuildService;

    public function __construct($id, $module, ParttimeBuildService $parttimeBuildService, $config = [])
    {
        $this->parttimeBuildService = new proxyService($parttimeBuildService);
        parent::__construct($id, $module, $config = []);
    }

    public function actionCreate()
    {
        $form = new ParttimeBuildForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $parttimeId = $this->parttimeBuildService->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(Url::previous());
        }

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }

    public function actionUpdate($id)
    {
        $parttime = $this->parttimeBuildService->get($id);
        $form = new ParttimeBuildForm($parttime);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->parttimeBuildService->update($id, $form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(Url::previous());
        }

        return $this->render('update', [
            'modelForm' => $form,
        ]);
    }

    public function actionDelete($id)
    {
        try {
            $this->parttimeBuildService->delete($id);
        } catch (\Exception $e) {
            return AjaxResponse::init(AjaxResponse::ERROR, $e->getMessage());
        }

        return AjaxResponse::init(AjaxResponse::SUCCESS);
    }
}
