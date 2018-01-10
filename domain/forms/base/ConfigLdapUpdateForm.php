<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 11:17
 */

namespace domain\forms\base;

use domain\models\base\ConfigLdap;
use domain\rules\base\ConfigLdapRules;
use yii\base\Model;

class ConfigLdapUpdateForm extends Model
{
    public $config_ldap_host;
    public $config_ldap_port;
    public $config_ldap_admin_login;
    public $config_ldap_admin_password;
    public $config_ldap_active;
    public $config_ldap_only_ldap_use;

    public function __construct(ConfigLdap $configLdap, $config = [])
    {
        $this->config_ldap_host = $configLdap->config_ldap_host;
        $this->config_ldap_port = $configLdap->config_ldap_port;
        $this->config_ldap_admin_login = $configLdap->config_ldap_admin_login;
        $this->config_ldap_admin_password = $configLdap->config_ldap_admin_password;
        $this->config_ldap_active = $configLdap->config_ldap_active;
        $this->config_ldap_only_ldap_use = $configLdap->config_ldap_only_ldap_use;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ConfigLdapRules::client();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return (new ConfigLdap())->attributeLabels();
    }
}