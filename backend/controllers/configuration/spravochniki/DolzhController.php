<?php

namespace backend\controllers\configuration\spravochniki;

use common\widgets\GridView\services\AjaxResponse;
use domain\forms\base\DolzhForm;
use domain\services\AjaxFilter;
use domain\services\base\DolzhService;
use domain\services\proxyService;
use Yii;
use domain\models\base\Dolzh;
use domain\models\base\search\DolzhSearch;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * DolzhController implements the CRUD actions for Dolzh model.
 */
class DolzhController extends Controller
{
    /**
     * @var DolzhService
     */
    private $dolzhService;

    public function __construct($id, $module, DolzhService $dolzhService, $config = [])
    {
        $this->dolzhService = $dolzhService;
        parent::__construct($id, $module, $config = []);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['index', 'create', 'update', 'delete'],
//                        'allow' => true,
//                        'roles' => ['dolzhEdit'],
//                    ],
//                ],
//            ],
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
     * Lists all Dolzh models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DolzhSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Dolzh model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $form = new DolzhForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->dolzhService->create($form)
        ) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }

    /**
     * Updates an existing Dolzh model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param resource $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $dolzhModel = $this->findModel($id);
        $form = new DolzhForm($dolzhModel);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->dolzhService->update($dolzhModel->primaryKey, $form)
        ) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'modelForm' => $form,
        ]);
    }

    /**
     * Deletes an existing Dolzh model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param resource $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->dolzhService->delete($id);
        } catch (\Exception $e) {
            return AjaxResponse::init(AjaxResponse::ERROR, $e->getMessage());
        }

        return AjaxResponse::init(AjaxResponse::SUCCESS);
    }

    /**
     * Finds the Dolzh model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param resource $id
     * @return Dolzh the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dolzh::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
