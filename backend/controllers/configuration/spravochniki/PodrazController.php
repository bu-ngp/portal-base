<?php

namespace backend\controllers\configuration\spravochniki;

use console\helpers\RbacHelper;
use domain\services\ProxyService;
use Yii;
use domain\models\base\search\PodrazSearch;
use yii\web\Controller;
use common\widgets\GridView\services\AjaxResponse;
use domain\forms\base\PodrazForm;
use domain\services\AjaxFilter;
use domain\services\base\PodrazService;
use yii\filters\ContentNegotiator;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * PodrazController implements the CRUD actions for Podraz model.
 */
class PodrazController extends Controller
{
    /**
     * @var PodrazService $service
     */
    private $service;

    public function __construct($id, $module, PodrazService $service, $config = [])
    {
        $this->service = new ProxyService($service);
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
                        'roles' => [RbacHelper::PODRAZ_EDIT],
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
        $searchModel = new PodrazSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $form = new PodrazForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->service->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }

    public function actionUpdate($id)
    {
        $podraz = $this->service->find($id);
        $form = new PodrazForm($podraz);

        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->service->update($podraz->primaryKey, $form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));
            return $this->redirect(['index']);
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
