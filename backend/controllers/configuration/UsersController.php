<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:50
 */

namespace backend\controllers\configuration;


use common\widgets\Breadcrumbs\Breadcrumbs;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\forms\base\UserFormUpdate;
use domain\models\base\Employee;
use domain\models\base\search\AllEmployeeSearch;
use domain\models\base\search\AuthItemSearch;
use domain\models\base\search\EmployeeHistorySearch;
use domain\models\base\search\EmployeeSearch;
use domain\models\base\search\UsersSearch;
use domain\services\base\PersonService;
use domain\services\proxyService;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\web\Controller;

class UsersController extends Controller
{
    /**
     * @var PersonService
     */
    private $personService;

    public function __construct($id, $module, PersonService $personService, $config = [])
    {
        $this->personService = new proxyService($personService);
        parent::__construct($id, $module, $config = []);
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
            && $userForm->validate()
            && $profileForm->load(Yii::$app->request->post())
            && $profileForm->validate()
            && $newPersonId = $this->personService->create($userForm, $profileForm)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('domain/person', 'Person is saved. Add speciality.'));
            Breadcrumbs::removeLastCrumb();

            return $this->redirect(['update', 'id' => Uuid::uuid2str($newPersonId)]);
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
        $user = $this->personService->get($id);
        $userFormUpdate = new UserFormUpdate($user);

        $profile = $this->personService->getProfile($id);
        $profileForm = new ProfileForm($this->personService->getProfile($id) === false ? null : $profile);

//        $searchModelEmployee = new EmployeeHistorySearch();
//        $dataProviderEmployee = $searchModelEmployee->search(Yii::$app->request->queryParams);
        $searchModelEmployee = new AllEmployeeSearch();
        $dataProviderEmployee = $searchModelEmployee->search(Yii::$app->request->queryParams);
        $searchModelAuthItem = new AuthItemSearch();
        $dataProviderAuthItem = $searchModelAuthItem->searchForUpdate(Yii::$app->request->queryParams);

        if ($userFormUpdate->load(Yii::$app->request->post())
            && $userFormUpdate->validate()
            && $profileForm->load(Yii::$app->request->post())
            && $profileForm->validate()
            && $this->personService->update(Uuid::str2uuid($id), $userFormUpdate, $profileForm)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(['index']);
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
}