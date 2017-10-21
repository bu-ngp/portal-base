<?php

namespace backend\controllers\configuration\spravochniki;

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\GridView\services\AjaxResponse;
use console\helpers\RbacHelper;
use domain\forms\base\DolzhForm;
use domain\services\AjaxFilter;
use domain\services\base\DolzhService;
use domain\services\proxyService;
use Yii;
use domain\models\base\search\DolzhSearch;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

/**
 * DolzhController implements the CRUD actions for Dolzh model.
 */
class DolzhController extends Controller
{
    /**
     * @var DolzhService
     */
    private $service;

    public function __construct($id, $module, DolzhService $service, $config = [])
    {
        $this->service = new proxyService($service);
        parent::__construct($id, $module, $config = []);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => [RbacHelper::AUTHORIZED],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => [RbacHelper::DOLZH_EDIT],
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

    public function actionIndex()
    {
        $searchModel = new DolzhSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $form = new DolzhForm();

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
        $dolzh = $this->service->find($id);
        $form = new DolzhForm($dolzh);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->service->update($dolzh->primaryKey, $form)
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
