<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 13.09.2017
 * Time: 10:31
 */

namespace backend\controllers\configuration;


use common\widgets\Breadcrumbs\Breadcrumbs;
use console\helpers\RbacHelper;
use domain\forms\base\ConfigLdapUpdateForm;
use domain\services\base\ConfigLdapService;
use domain\services\ProxyService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ConfigLdapController extends Controller
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
                        'actions' => ['update'],
                        'roles' => [RbacHelper::ROLE_EDIT],
                    ],
                ],
            ],
        ];
    }

    /**
     * @var ConfigLdapService
     */
    private $service;

    public function __construct($id, $module, ConfigLdapService $service, $config = [])
    {
        $this->service = new ProxyService($service);
        parent::__construct($id, $module, $config = []);
    }

    public function actionUpdate()
    {
        $configLdap = $this->service->get();
        $form = new ConfigLdapUpdateForm($configLdap);

        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->service->update($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));
            return $this->redirect(Breadcrumbs::previousUrl());
        }

        $form->config_ldap_admin_password = NULL;

        return $this->render('update', ['modelForm' => $form]);
    }
}