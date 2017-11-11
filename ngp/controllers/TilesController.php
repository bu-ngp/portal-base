<?php

namespace ngp\controllers;

use ngp\helpers\RbacHelper;
use Yii;
use ngp\services\models\Tiles;
use ngp\services\models\search\TilesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\widgets\GridView\services\AjaxResponse;
use ngp\services\forms\TilesForm;
use domain\services\AjaxFilter;
use ngp\services\services\TilesService;
use domain\services\proxyService;
use yii\filters\ContentNegotiator;
use yii\filters\AccessControl;
use yii\web\Response;
use common\widgets\Breadcrumbs\Breadcrumbs;

/**
 * TilesController implements the CRUD actions for Tiles model.
 */
class TilesController extends Controller
{
    /** @var TilesService $service */
    private $service;

    public function __construct($id, $module, TilesService $service, $config = [])
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
                        'actions' => ['index', 'create', 'update', 'delete','uploadPhoto'],
                        'roles' => [RbacHelper::TILES_EDIT],
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
        $searchModel = new TilesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $form = new TilesForm();

        if (false && $form->load(Yii::$app->request->post())
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
        $tiles = $this->service->find($id);
        $form = new TilesForm($tiles);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->service->update($tiles->primaryKey, $form)
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
