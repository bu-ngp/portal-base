<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:50
 */

namespace backend\controllers\configuration;


use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\GridView\services\AjaxResponse;
use console\helpers\RbacHelper;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\forms\base\UserFormUpdate;
use domain\models\base\search\AllEmployeeSearch;
use domain\models\base\search\AuthItemSearch;
use domain\models\base\search\UsersSearch;
use domain\services\AjaxFilter;
use domain\services\base\PersonService;
use domain\services\proxyService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

class UsersController extends Controller
{
    /**
     * @var PersonService
     */
    private $service;

    public function __construct($id, $module, PersonService $service, $config = [])
    {
        $this->service = new proxyService($service);
        parent::__construct($id, $module, $config = []);
    }

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
                        'roles' => [RbacHelper::USER_EDIT],
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
        $searchModel = new UsersSearch();
        // $filterModel = new AuthItemFilter();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //    'filterModel' => $filterModel,
        ]);
    }

    public function actionCreate()
    {
        $userForm = new UserForm();
        $profileForm = new ProfileForm();

        $searchModelAuthItem = new AuthItemSearch();
        $dataProviderAuthItem = $searchModelAuthItem->searchForCreate(Yii::$app->request->queryParams);

        if ($userForm->load(Yii::$app->request->post())
            && $profileForm->load(Yii::$app->request->post())
            && $userForm->validate() & $profileForm->validate()
            && $newPersonId = $this->service->create($userForm, $profileForm)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('domain/person', 'Person is saved. Add speciality.'));
            Breadcrumbs::removeLastCrumb();

            return $this->redirect(['update', 'id' => $newPersonId]);
        }

        $userForm->person_password = null;
        $userForm->person_password_repeat = null;

        return $this->render('create', [
            'modelUserForm' => $userForm,
            'modelProfileForm' => $profileForm,
            'searchModelAuthItem' => $searchModelAuthItem,
            'dataProviderAuthItem' => $dataProviderAuthItem,
        ]);
    }

    public function actionUpdate($id)
    {
        $user = $this->service->getUser($id);
        $userFormUpdate = new UserFormUpdate($user);

        $profile = $this->service->getProfile($id);
        $profileForm = new ProfileForm($this->service->getProfile($id) === false ? null : $profile);

        $searchModelEmployee = new AllEmployeeSearch();
        $dataProviderEmployee = $searchModelEmployee->search(Yii::$app->request->queryParams);
        $searchModelAuthItem = new AuthItemSearch();
        $dataProviderAuthItem = $searchModelAuthItem->searchForUpdate(Yii::$app->request->queryParams);

        if ($userFormUpdate->load(Yii::$app->request->post())
            && $profileForm->load(Yii::$app->request->post())
            && $userFormUpdate->validate() & $profileForm->validate()
            && $this->service->update($user->primaryKey, $userFormUpdate, $profileForm)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(Breadcrumbs::previousUrl());
        }

        return $this->render('update', [
            'modelUserFormUpdate' => $userFormUpdate,
            'modelProfileForm' => $profileForm,
            'searchModelEmployee' => $searchModelEmployee,
            'dataProviderEmployee' => $dataProviderEmployee,
            'searchModelAuthItem' => $searchModelAuthItem,
            'dataProviderAuthItem' => $dataProviderAuthItem,
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