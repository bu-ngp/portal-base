<?php

namespace backend\controllers\configuration\spravochniki;

use Yii;
use domain\models\base\Build;
use domain\models\base\search\BuildSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\widgets\GridView\services\AjaxResponse;
use domain\forms\base\BuildForm;
use domain\services\AjaxFilter;
use domain\services\base\BuildService;
use domain\services\proxyService;
use yii\filters\ContentNegotiator;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * BuildController implements the CRUD actions for Build model.
 */
class BuildController extends Controller
{
    /**
    * @var BuildService $buildService
    */
    private $buildService;

    public function __construct($id, $module, BuildService $buildService, $config = [])
    {
        $this->buildService = new proxyService($buildService);
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
     * Lists all Build models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BuildSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Build model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $form = new BuildForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->buildService->create($form->build_name)
        ) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }

    /**
     * Updates an existing Build model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param resource $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $buildModel = $this->findModel($id);
        $form = new BuildForm($buildModel);

        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->buildService->update($buildModel->primaryKey, $form->build_name)
        ) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'modelForm' => $form,
        ]);
    }

    /**
     * Deletes an existing Build model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param resource $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->buildService->delete($id);
        } catch (\Exception $e) {
            return AjaxResponse::init(AjaxResponse::ERROR, $e->getMessage());
        }

        return AjaxResponse::init(AjaxResponse::SUCCESS);
    }

    /**
     * Finds the Build model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param resource $id
     * @return Build the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Build::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
