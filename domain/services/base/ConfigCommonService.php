<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use domain\forms\base\ConfigCommonUpdateForm;
use domain\repositories\base\ConfigCommonRepository;
use domain\services\Service;

class ConfigCommonService extends Service
{
    private $ConfigCommons;

    public function __construct(
        ConfigCommonRepository $ConfigCommons
    )
    {
        $this->ConfigCommons = $ConfigCommons;
    }

    public function get()
    {
        return $this->ConfigCommons->find();
    }

    public function update(ConfigCommonUpdateForm $form)
    {
        $ConfigCommon = $this->ConfigCommons->find();
        $ConfigCommon->edit($form);

        $this->ConfigCommons->save($ConfigCommon);
    }

    public function getPortalMail()
    {
        $configCommon = $this->ConfigCommons->find();
        return $configCommon->config_common_portal_mail;
    }

    public function getAdministratorMails()
    {
        $configCommon = $this->ConfigCommons->find();

        $mails = explode(',', $configCommon->config_common_mail_administrators);
        $mails = array_filter($mails, 'trim');

        return $mails ?: [];
    }
}