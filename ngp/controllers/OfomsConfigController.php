<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 15:03
 */

namespace ngp\controllers;

use common\widgets\Breadcrumbs\Breadcrumbs;
use console\helpers\RbacHelper;
use domain\services\ProxyService;
use ngp\services\forms\ConfigOfomsUpdateForm;
use ngp\services\services\ConfigOfomsService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class OfomsConfigController extends Controller
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
     * @var ConfigOfomsService
     */
    private $service;

    public function __construct($id, $module, ConfigOfomsService $service, $config = [])
    {
        $this->service = new ProxyService($service);
        parent::__construct($id, $module, $config = []);
    }

    public function actionIndex()
    {
        $configLdap = $this->service->get();
        $form = new ConfigOfomsUpdateForm($configLdap);

        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->service->update($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));
            return $this->redirect(Breadcrumbs::previousUrl());
        }

        $form->config_ofoms_password = NULL;

        return $this->render('index', ['modelForm' => $form]);
    }
}