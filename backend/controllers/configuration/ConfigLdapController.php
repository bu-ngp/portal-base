<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 13.09.2017
 * Time: 10:31
 */

namespace backend\controllers\configuration;


use common\widgets\NotifyShower\NotifyShower;
use domain\forms\base\ConfigLdapUpdateForm;
use domain\models\base\ConfigLdap;
use domain\services\base\ConfigLdapService;
use domain\services\proxyService;
use Yii;
use yii\web\Controller;

class ConfigLdapController extends Controller
{
    /**
     * @var ConfigLdapService
     */
    private $configLdapService;

    public function __construct($id, $module, ConfigLdapService $configLdapService, $config = [])
    {
        $this->configLdapService = new proxyService($configLdapService);
        parent::__construct($id, $module, $config = []);
    }

    public function actionUpdate()
    {
        $configLdapModel = ConfigLdap::findOne(1);
        $form = new ConfigLdapUpdateForm($configLdapModel);

        if ($form->load(Yii::$app->request->post()) && $form->validate()
            && $this->configLdapService->update(
                $form->config_ldap_host,
                $form->config_ldap_port,
                $form->config_ldap_admin_login,
                $form->config_ldap_admin_password,
                $form->config_ldap_active
            )
        ) {
            return $this->redirect(['configuration/config-auth/index']);
        }

        $form->config_ldap_admin_password = NULL;

        return $this->render('update', ['modelForm' => $form]);
    }
}