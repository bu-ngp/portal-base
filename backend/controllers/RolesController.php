<?php

namespace backend\controllers;

use common\widgets\Breadcrumbs\Breadcrumbs;
use domain\forms\base\RoleForm;
use domain\forms\base\RoleUpdateForm;
use domain\models\base\AuthItemChild;
use domain\models\base\filter\AuthItemFilter;
use common\widgets\ReportLoader\ReportByModel;
use domain\models\base\search\AuthItemChildSearch;
use domain\services\base\RoleService;
use domain\services\proxyService;
use common\reports\RolesReport;
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

    public function actionIndexForRoles()
    {
        $searchModel = new AuthItemSearch();
        $filterModel = new AuthItemFilter();
        $dataProvider = $searchModel->searchForRoles(Yii::$app->request->queryParams);

        return $this->renderAjax('index_for_roles', [
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

    public function actionUpdateRemoveRoles($id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($wkchoose = Yii::$app->request->getHeaders()->get('wk-choose')) {
                $_choose = json_decode($wkchoose);

                $items = AuthItem::find()
                    ->andWhere($_choose->checkAll ? ['not in', 'name', $_choose->excluded] : ['in', 'name', $_choose->included])
                    ->all();

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $auth = Yii::$app->authManager;

                    /** @var AuthItem $item */
                    foreach ($items as $item) {
                        $auth->addChild($auth->getRole($id), $auth->getRole($item->name));
                    }

                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    return (object)[
                        'result' => 'error',
                        'message' => 'Ошибка при добавлении записей',
                    ];
                }

                return (object)[
                    'result' => 'success',
                ];
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
