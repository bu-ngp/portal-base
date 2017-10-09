<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use common\classes\Ldap;
use common\widgets\NotifyShower\NotifyShower;
use domain\exceptions\ServiceErrorsException;
use domain\repositories\base\ConfigLdapRepository;
use domain\services\BaseService;
use Yii;

class ConfigLdapService extends BaseService
{
    private $configLdapRepository;

    public function __construct(
        ConfigLdapRepository $configLdapRepository
    )
    {
        $this->configLdapRepository = $configLdapRepository;

        parent::__construct();
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

                    return $this->configLdapRepository->save($configLdap);
                } else {
                    NotifyShower::message(\Yii::t('common/config-ldap', "LDAP can't connect"));
                }
            } catch (\Exception $e) {
                NotifyShower::message(\Yii::t('common/config-ldap', "LDAP can't connect"));
            }
        } else {
            NotifyShower::message(\Yii::t('common/config-ldap', 'Ldap config not correct'));
        }

        return false;
    }
}