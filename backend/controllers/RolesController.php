<?php

namespace backend\controllers;

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\GridView\services\AjaxResponse;
use common\widgets\NotifyShower\NotifyShower;
use domain\forms\base\RoleForm;
use domain\forms\base\RoleUpdateForm;
use domain\models\base\AuthItemChild;
use domain\models\base\filter\AuthItemFilter;
use common\widgets\ReportLoader\ReportByModel;
use domain\models\base\search\AuthItemChildSearch;
use domain\services\base\RoleService;
use domain\services\proxyService;
use common\reports\RolesReport;
use Knp\Snappy\PdfTest;
use Yii;
use domain\models\base\AuthItem;
use domain\models\base\search\AuthItemSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

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

        return $this->render('_create', [
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
        $roleModel = $this->findModel($id);
        $form = new RoleUpdateForm($roleModel);
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->searchForUpdate(Yii::$app->request->queryParams);

        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->roleService->update(
                $roleModel->primaryKey,
                $form->description
            )
        ) {
            return $this->redirect(['index']);
        }

        $form->addErrors($this->roleService->getErrors());

        return $this->render('_update', [
            'modelForm' => $form,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /** Грид для выбора записей */
    public function actionIndexForRoles()
    {
        $searchModel = new AuthItemSearch();
        $filterModel = new AuthItemFilter();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index_for_roles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
        ]);
    }

    /** Удаление роли на форме редактирования записи */
    public function actionDeleteRole($id, $mainId)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $authItemChildModel = AuthItemChild::find()->where(['parent' => $mainId, 'child' => $id])->one();
            $result = $authItemChildModel->delete();

            if ($result === false) {
                return AjaxResponse::init(AjaxResponse::ERROR, Yii::t('common/roles', 'Delete error'));
            } elseif ($result === 0) {
                return AjaxResponse::init(AjaxResponse::ERROR, Yii::t('common/roles', 'Deleted 0 records'));
            } else {
                return AjaxResponse::init(AjaxResponse::SUCCESS);
            }
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

    public function actionReport()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return RolesReport::lets()
                ->assignTemplate('rolesTemplate.xlsx')
                ->params(['view' => 1])
                ->type('pdf')
                ->save();
        } else {
            throw new \Exception('Only Ajax Requests');
        }
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
