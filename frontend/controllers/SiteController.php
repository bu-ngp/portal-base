<?php
namespace frontend\controllers;

use common\widgets\CardList\CardListHelper;
use domain\models\base\search\AuthItemSearch;
use domain\services\base\PersonService;
use domain\services\proxyService;
use Faker\Factory;
use ReflectionClass;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\base\InvalidParamException;
use yii\bootstrap\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
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

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $modelSearch = new AuthItemSearch();
        return $this->render('index', ['modelSearch' => $modelSearch]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $form = new \domain\forms\LoginForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->personService->login($form->person_username, $form->person_password, $form->person_rememberMe)
        ) {
            return $this->goBack();
        }

        $form->addErrors($this->personService->getErrors());

        return $this->render('login', [
            'LoginForm' => $form,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        $this->personService->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionTest2()
    {
        $faker = Factory::create('ru_RU');

        $oClass = new ReflectionClass(FA::class);


//        Yii::$app->response->format = Response::FORMAT_JSON;
//        return [
//            '<div class="grid-item col-md-4"></div>',
//            '<div class="grid-item col-md-4"></div>',
//            '<div class="grid-item col-md-4"></div>',
//        ];
        $content = '';
        /** @var array $card */
        for ($i = 1; $i <= 6; $i++) {
            $contsFA = array_rand($oClass->getConstants());
            $valueConst = $oClass->getConstant($contsFA);

            $mediaContent = FA::icon($valueConst, ['class' => 'wk-style']);

            $titleContent = Html::tag('h2', Html::encode($faker->company), ['class' => 'pmd-card-title-text'])
                . Html::tag('span', Html::encode($faker->realText(rand(40, 200))), ['class' => 'pmd-card-subtitle-text']);

            $actionsContent = Html::a('ПЕРЕЙТИ', '#', ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary']);

            $ar1 = ['wk-red-style', 'wk-blue-style', 'wk-yellow-style', 'wk-green-style', 'wk-grey-style'];

            $media = Html::tag('div', $mediaContent, ['class' => 'pmd-card-media ' . $ar1[rand(0, 4)]]);

            $title = Html::tag('div', $titleContent, ['class' => 'pmd-card-title']);
            $actions = Html::tag('div', $actionsContent, ['class' => 'pmd-card-actions']);

            $content1 = Html::tag('div', $media . $title . $actions, ['class' => 'pmd-card pmd-card-default pmd-z-depth']);

            $content .= Html::tag('div', $content1, ['class' => 'col-xs-12 col-sm-6 col-md-4 wk-widget-card wk-widget-show wk-widget-scroll']);


            /* if ($i % 3 == 0) {
                 $content .= Html::tag('div', '', ['class' => 'col-xs-12 wk-widget-card wk-widget-hide']);
             }*/
        }
        echo $content;

    }

    public function actionTest()
    {
     //   $faker = Factory::create('ru_RU');
     //   $oClass = new ReflectionClass(FA::class);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return CardListHelper::createAjaxCards($dataProvider, 'name', '', '', 'description', '', 'name');


    }
}
