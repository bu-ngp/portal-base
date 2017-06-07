<?php

namespace backend\controllers;

use domain\forms\base\RoleForm;
use domain\models\base\filter\AuthItemFilter;
use domain\proc\ReportByModel;
use domain\services\base\RoleService;
use domain\services\proxyService;
use Yii;
use domain\models\base\AuthItem;
use domain\models\base\search\AuthItemSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class RolesController extends Controller
{
    /**
     * @var RoleService
     */
    private $roleService;

    public function __construct($id, $module, RoleService $roleService, $config = [])
    {
        $this->roleService = new proxyService($roleService);
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
                        //  'actions' => ['index'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch();
        $filterModel = new AuthItemFilter();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
        ]);
    }

    public function actionReport()
    {
        $searchModel = new AuthItemSearch();
        $filterModel = new AuthItemFilter();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $report = new ReportByModel($dataProvider);
        $report->reportDisplayName = 'Роли';
        $report->reportid = $searchModel->formName();
        $report->reportType = ReportByModel::EXCEL;
        $report->report();
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $form = new RoleForm();
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->searchForCreate(Yii::$app->request->queryParams);

        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->roleService->create(
                $form->name,
                $form->description,
                $form->type,
                $form->assignRoles
            )
        ) {
            return $this->redirect(['index']);
        }

        $form->addErrors($this->roleService->getErrors());

        return $this->render('create', [
            'modelForm' => $form,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
