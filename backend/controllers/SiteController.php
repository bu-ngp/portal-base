<?php

namespace backend\controllers;

use doh\services\classes\DoH;
use domain\forms\AcceptanceTestForm;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\models\base\Dolzh;
use domain\models\base\filter\AuthItemTestFilter;
use domain\models\base\Podraz;
use domain\models\base\search\AuthItemSearch;
use domain\models\base\search\AuthItemTestSearch;
use domain\models\base\search\BuildSearch;
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
                        'ips' => ['127.0.0.1', 'localhost', '::1', '192.168.0.100', '172.19.17.81'],
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
        $testForm = new AcceptanceTestForm();

        $searchModelChooseBuild = new BuildSearch();
        $dataProviderChooseBuild = $searchModelChooseBuild->search(Yii::$app->request->queryParams);

        $searchModelAuthitem = new AuthItemTestSearch();
        $filterModelAuthitem = new AuthItemTestFilter();
        $dataProviderAuthitem = $searchModelAuthitem->search(Yii::$app->request->queryParams);

        return $this->render('test', [
            'testForm' => $testForm,
            'searchModelChooseBuild' => $searchModelChooseBuild,
            'dataProviderChooseBuild' => $dataProviderChooseBuild,
            'searchModelAuthitem' => $searchModelAuthitem,
            'dataProviderAuthitem' => $dataProviderAuthitem,
            'filterModelAuthitem' => $filterModelAuthitem,
        ]);
    }

    public function actionTest()
    {
//        $doh = new DoH(new EmployeeProccessLoader(Yii::getAlias('@common/ftpimport/Upload_Kamin_SotrInfo3.xlsx')));
//        $doh->execute();
    }
}
