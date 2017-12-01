<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 13.09.2017
 * Time: 10:19
 */

namespace backend\controllers\configuration;


use common\widgets\Breadcrumbs\Breadcrumbs;
use console\helpers\RbacHelper;
use domain\forms\base\ConfigCommonUpdateForm;
use domain\services\base\ConfigCommonService;
use domain\services\ProxyService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ConfigCommonController extends Controller
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
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => [RbacHelper::ADMINISTRATOR],
                    ],
                ],
            ],
        ];
    }

    /**
     * @var ConfigCommonService
     */
    private $service;

    public function __construct($id, $module, ConfigCommonService $service, $config = [])
    {
        $this->service = new ProxyService($service);
        parent::__construct($id, $module, $config = []);
    }

    public function actionIndex()
    {
        $configCommon = $this->service->get();
        $form = new ConfigCommonUpdateForm($configCommon);

        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->service->update($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));
            return $this->redirect(Breadcrumbs::previousUrl());
        }

        return $this->render('update', ['modelForm' => $form]);
    }
}