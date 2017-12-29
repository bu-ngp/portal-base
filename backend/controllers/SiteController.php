<?php

namespace backend\controllers;

use doh\services\classes\DoH;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\models\base\search\AuthItemSearch;
use domain\proccesses\EmployeeProccessLoader;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use domain\forms\base\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
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
                        'actions' => ['login', 'error', 'test'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionTest()
    {
//        $service = Yii::createObject('domain\services\base\PersonService');
//        $profileForm = new ProfileForm();
//        $userForm = new UserForm([
//            'person_fullname' => '',
//            'person_username' => '',
//            'person_password' => '111111',
//            'person_password_repeat' => '2',
//            'assignRoles' => '[]',
//        ]);
//
//        try {
//            $service->create($userForm, $profileForm);
//        } catch (\Exception $e) {
//
//        }
//
//        return VarDumper::dumpAsString($userForm->getErrors(), 10, true);


    }
}
