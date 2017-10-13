<?php

namespace backend\controllers\configuration;

use common\widgets\GridView\services\AjaxResponse;
use domain\forms\base\RoleForm;
use domain\forms\base\RoleUpdateForm;
use domain\models\base\filter\AuthItemFilter;
use domain\services\AjaxFilter;
use domain\services\base\RoleService;
use common\reports\RolesReport;
use domain\services\proxyService;
use Yii;
use domain\models\base\AuthItem;
use domain\models\base\search\AuthItemSearch;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
                        'actions' => ['index', 'create', 'update', 'index-for-roles', 'index-for-users', 'delete-role', 'delete'],
                        'allow' => true,
                    ],
                ],
            ],
            [
                'class' => AjaxFilter::className(),
                'actions' => ['delete-role', 'delete', 'report'],
            ],
            [
                'class' => ContentNegotiator::className(),
                'only' => ['delete-role', 'delete', 'report'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
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

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->roleService->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(['index']);
        }

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
        $roleModel = $this->roleService->find($id);
        $form = new RoleUpdateForm($roleModel);
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->searchForUpdate(Yii::$app->request->queryParams);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->roleService->update($roleModel->primaryKey, $form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(['index']);
        }

        return $this->render('update', [
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
        $gridExcludeIdsFunc = AuthItem::funcExcludeForRoles();

        return $this->render('index_for_roles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'gridExcludeIdsFunc' => $gridExcludeIdsFunc,
        ]);
    }

    public function actionIndexForUsers()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $gridExcludeIdsFunc = AuthItem::funcExcludeForRoles();

        return $this->render('index_for_users', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'gridExcludeIdsFunc' => $gridExcludeIdsFunc,
        ]);
    }

    /** Удаление роли на форме редактирования записи */
    public function actionDeleteRole($mainId, $id)
    {
        try {
            $this->roleService->removeRoleForUpdate($mainId, $id);
        } catch (\Exception $e) {
            return AjaxResponse::init(AjaxResponse::ERROR, $e->getMessage());
        }

        return AjaxResponse::init(AjaxResponse::SUCCESS);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->roleService->removeRole($id);
        } catch (\Exception $e) {
            return AjaxResponse::init(AjaxResponse::ERROR, $e->getMessage());
        }

        return AjaxResponse::init(AjaxResponse::SUCCESS);
    }

    public function actionReport()
    {
        return RolesReport::lets()
            ->assignTemplate('rolesTemplate.xlsx')
            ->params(['view' => 1])
            ->type('pdf')
            ->save();
    }
}
