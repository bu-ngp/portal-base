<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 04.12.2017
 * Time: 9:08
 */

namespace domain\services;


use domain\models\base\ConfigCommon;
use yii\base\Component;

class ConfigComponent extends Component
{
    public $config_common_portal_mail;
    public $config_common_mail_administrators;
    public $config_common_footer_company;
    public $config_common_footer_addition;

    public function __construct($config = [])
    {
        $configCommon = ConfigCommon::findOne(1);

        $this->config_common_portal_mail = $configCommon->config_common_portal_mail;
        $this->config_common_mail_administrators = $configCommon->config_common_mail_administrators;
        $this->config_common_footer_company = $configCommon->config_common_footer_company;
        $this->config_common_footer_addition = $configCommon->config_common_footer_addition;

        parent::__construct($config);
    }
}