<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:50
 */

namespace backend\controllers\configuration;


use common\widgets\NotifyShower\NotifyShower;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\models\base\search\AuthItemSearch;
use domain\models\base\search\EmployeeSearch;
use domain\models\base\search\UsersSearch;
use domain\services\base\dto\PersonData;
use domain\services\base\dto\ProfileData;
use domain\services\base\PersonService;
use domain\services\proxyService;
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

        $searchModelEmployee = new EmployeeSearch();
        $dataProviderEmployee = $searchModelEmployee->search(Yii::$app->request->queryParams);
        $searchModelAuthItem = new AuthItemSearch();
        $dataProviderAuthItem = $searchModelAuthItem->searchForCreate(Yii::$app->request->queryParams);

        if ($userForm->load(Yii::$app->request->post())
            && $userForm->validate()
            && $profileForm->load(Yii::$app->request->post())
            && $profileForm->validate()
            && $this->personService->create(
                new PersonData(
                    $userForm->person_fullname,
                    $userForm->person_username,
                    $userForm->person_password,
                    $userForm->person_password_repeat,
                    $userForm->person_email,
                    $userForm->person_fired
                ),
                new ProfileData(
                    $profileForm->profile_inn,
                    $profileForm->profile_dr,
                    $profileForm->profile_pol,
                    $profileForm->profile_snils,
                    $profileForm->profile_address
                ),
                $userForm->assignEmployees,
                $userForm->assignRoles
            )
        ) {
            return $this->redirect(['index']);
        }

        NotifyShower::serviceMessages($this->personService->getErrors());

        return $this->render('create', [
            'modelUserForm' => $userForm,
            'modelProfileForm' => $profileForm,
            'searchModelEmployee' => $searchModelEmployee,
            'dataProviderEmployee' => $dataProviderEmployee,
            'searchModelAuthItem' => $searchModelAuthItem,
            'dataProviderAuthItem' => $dataProviderAuthItem,
        ]);
    }
}