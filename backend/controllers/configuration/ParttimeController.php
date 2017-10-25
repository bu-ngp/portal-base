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
use domain\forms\base\ParttimeForm;
use domain\models\base\search\BuildSearch;
use domain\models\base\search\ParttimeBuildSearch;
use domain\services\AjaxFilter;
use domain\services\base\ParttimeService;
use domain\services\proxyService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

class ParttimeController extends Controller
{
    /**
     * @var ParttimeService
     */
    private $service;

    public function __construct($id, $module, ParttimeService $service, $config = [])
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
        $form = new ParttimeForm();

        $searchModelBuild = new BuildSearch();
        $dataProviderBuild = $searchModelBuild->search(Yii::$app->request->queryParams);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->service->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));
            return $this->redirect(Breadcrumbs::previousUrl());
        }

        return $this->render('create', [
            'modelForm' => $form,
            'searchModelBuild' => $searchModelBuild,
            'dataProviderBuild' => $dataProviderBuild,
        ]);
    }

    public function actionUpdate($id)
    {
        $employee = $this->service->get($id);
        $form = new ParttimeForm($employee);

        $searchModelParttimeBuild = new ParttimeBuildSearch();
        $dataProviderParttimeBuild = $searchModelParttimeBuild->search(Yii::$app->request->queryParams);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->service->update($id, $form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));
            return $this->redirect(Breadcrumbs::previousUrl());
        }

        return $this->render('update', [
            'modelForm' => $form,
            'searchModelParttimeBuild' => $searchModelParttimeBuild,
            'dataProviderParttimeBuild' => $dataProviderParttimeBuild,
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