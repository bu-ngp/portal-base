<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 17:03
 */

namespace domain\rules\base;


class ConfigLdapRules
{
    public static function client()
    {
        return
            [
                [['config_ldap_port'], 'default', 'value' => 389],
                [['config_ldap_active'], 'default', 'value' => 0],
                [['config_ldap_port', 'config_ldap_active'], 'required'],
                [['config_ldap_port'], 'integer', 'min' => 0, 'max' => 65535],
                [['config_ldap_active'], 'boolean'],
                [['config_ldap_host', 'config_ldap_admin_login', 'config_ldap_admin_password'], 'string', 'max' => 255],
                [['config_ldap_host', 'config_ldap_port', 'config_ldap_admin_login', 'config_ldap_admin_password'], 'required', 'when' => function ($model) {
                    return $model->config_ldap_active;
                }, 'enableClientValidation' => false],
            ];
    }
}