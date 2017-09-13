<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use domain\repositories\base\ConfigLdapRepository;
use domain\repositories\base\PersonRepository;
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
        $configLdap = $this->configLdapRepository->find();
        $configLdap->editData($ldapHost, $ldapPort, $ldapActive);
        $this->configLdapRepository->save($configLdap);

        return true;
    }
}