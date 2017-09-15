<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use domain\exceptions\ServiceErrorsException;
use domain\repositories\base\ConfigLdapRepository;
use domain\services\BaseService;

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

    public function update($ldapHost, $ldapPort = 389, $ldapActive = false)
    {
        if ($ds = ldap_connect($ldapHost, $ldapPort)) {
            try {
                if (ldap_bind($ds)) {
                    ldap_close($ds);
                    $configLdap = $this->configLdapRepository->find();
                    $configLdap->editData($ldapHost, $ldapPort, $ldapActive);
                    $this->configLdapRepository->save($configLdap);
                } else {
                    throw new ServiceErrorsException('notifyShower', \Yii::t('common/config-ldap', "LDAP can't connect"));
                }
            } catch (\Exception $e) {
                throw new ServiceErrorsException('notifyShower', \Yii::t('common/config-ldap', "LDAP can't connect"));
            }
        } else {
            throw new ServiceErrorsException('notifyShower', \Yii::t('common/config-ldap', 'Ldap config not correct'));
        }

        return true;
    }
}