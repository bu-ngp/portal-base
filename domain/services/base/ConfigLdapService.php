<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use common\classes\Ldap;
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

    public function update($ldapHost, $ldapPort = 389, $ldapAdminLogin, $ldapAdminPassword, $ldapActive = false)
    {
        $domain = Ldap::getDomain($ldapHost);

        if ($ds = ldap_connect($ldapHost, $ldapPort)) {
            ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

            try {
                if (ldap_bind($ds, $domain . $ldapAdminLogin, $ldapAdminPassword)) {
                    ldap_close($ds);
                    $configLdap = $this->configLdapRepository->find();
                    $configLdap->editData($ldapHost, $ldapPort, $ldapAdminLogin, $ldapAdminPassword, $ldapActive);

                    $this->configLdapRepository->save($configLdap);
                } else {
                    throw new \DomainException(\Yii::t('common/config-ldap', "LDAP can't connect"));
                }
            } catch (\Exception $e) {
                throw new \DomainException(\Yii::t('common/config-ldap', "LDAP can't connect"));
            }
        } else {
            throw new \DomainException(\Yii::t('common/config-ldap', 'Ldap config not correct'));
        }
    }
}