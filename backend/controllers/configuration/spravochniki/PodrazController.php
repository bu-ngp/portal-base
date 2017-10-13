<?php

namespace backend\controllers\configuration\spravochniki;

use domain\services\proxyService;
use Yii;
use domain\models\base\Podraz;
use domain\models\base\search\PodrazSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
     * @var PodrazService $podrazService
     */
    private $podrazService;

    public function __construct($id, $module, PodrazService $podrazService, $config = [])
    {
        $this->podrazService = new proxyService($podrazService);
        parent::__construct($id, $module, $config = []);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
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

    /**
     * Lists all Podraz models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PodrazSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Podraz model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $form = new PodrazForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->podrazService->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }

    /**
     * Updates an existing Podraz model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param resource $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $podrazModel = $this->podrazService->find($id);
        $form = new PodrazForm($podrazModel);

        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->podrazService->update($podrazModel->primaryKey, $form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'modelForm' => $form,
        ]);
    }

    /**
     * Deletes an existing Podraz model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param resource $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->podrazService->delete($id);
        } catch (\Exception $e) {
            return AjaxResponse::init(AjaxResponse::ERROR, $e->getMessage());
        }

        return AjaxResponse::init(AjaxResponse::SUCCESS);
    }
}
