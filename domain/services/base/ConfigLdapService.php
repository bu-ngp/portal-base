<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use domain\auth\Ldap;
use domain\forms\base\ConfigLdapUpdateForm;
use domain\repositories\base\ConfigLdapRepository;
use Yii;

class ConfigLdapService
{
    private $configLdapRepository;

    public function __construct(
        ConfigLdapRepository $configLdapRepository
    )
    {
        $this->configLdapRepository = $configLdapRepository;
    }

    public function get()
    {
        return $this->configLdapRepository->find();
    }

    public function update(ConfigLdapUpdateForm $form)
    {
        $domain = Ldap::getDomain($form->config_ldap_host);

        if ($ds = ldap_connect($form->config_ldap_host, $form->config_ldap_port)) {
            ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

            try {
                if (ldap_bind($ds, $domain . $form->config_ldap_admin_login, $form->config_ldap_admin_password)) {
                    ldap_close($ds);
                    $configLdap = $this->configLdapRepository->find();
                    $configLdap->edit($form);

                    $this->configLdapRepository->save($configLdap);
                } else {
                    throw new \DomainException(Yii::t('common/config-ldap', "LDAP can't connect"));
                }
            } catch (\Exception $e) {
                throw new \DomainException(Yii::t('common/config-ldap', "LDAP can't connect"));
            }
        } else {
            throw new \DomainException(Yii::t('common/config-ldap', 'Ldap config not correct'));
        }
    }
}