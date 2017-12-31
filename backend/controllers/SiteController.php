<?php

namespace backend\controllers;

use doh\services\classes\DoH;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\models\base\Dolzh;
use domain\models\base\Podraz;
use domain\models\base\search\AuthItemSearch;
use domain\proccesses\EmployeeProccessLoader;
use Yii;
use yii\base\DynamicModel;
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
                    [
                        'actions' => ['acceptance-test'],
                        'allow' => true,
                        'ips' => ['127.0.0.1', 'localhost', '::1', '192.168.0.100'],
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

    public function actionAcceptanceTest()
    {
        $dolzhModel = new Dolzh();
        $dolzhModelMultiple = new Dolzh();

        $podrazModel = Podraz::find()->all();

        return $this->render('test', [
            'dolzhModel' => $dolzhModel,
            'podrazModel' => $podrazModel,
            'dolzhModelMultiple' => $dolzhModelMultiple,
        ]);
    }

    public function actionTest()
    {
        $service = Yii::createObject('domain\services\base\PersonService');

        $userForm = new UserForm([
            'person_fullname' => 'Иванов Иван Иванович',
            'person_username' => 'IvanovII3',
            'person_password' => '111111',
            'person_email' => 'mail@mail.ru',
            'assignRoles' => '["baseDolzhEdited"]',
            //'assignRoles' => '["baseDolzhEdit","basePodrazEdit"]',
        ]);
        $profileForm = new ProfileForm();

        try {
            $service->create($userForm, $profileForm);
        } catch (\Exception $e) {
            echo VarDumper::dumpAsString($e->getMessage(), 10, true);
        }

        echo VarDumper::dumpAsString($userForm->getErrors(), 10, true);


    }
}
