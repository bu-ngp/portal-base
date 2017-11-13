<?php

namespace ngp\services\forms;

use ngp\services\models\ConfigOfoms;
use ngp\services\rules\ConfigOfomsRules;
use yii\base\Model;

class ConfigOfomsUpdateForm extends Model
{
    public $config_ofoms_url;
    public $config_ofoms_url_prik;
    public $config_ofoms_login;
    public $config_ofoms_password;
    public $config_ofoms_remote_host_name;
    public $config_ofoms_active;

    public function __construct(ConfigOfoms $configOfoms, $config = [])
    {
        $this->config_ofoms_url = $configOfoms->config_ofoms_url;
        $this->config_ofoms_url_prik = $configOfoms->config_ofoms_url_prik;
        $this->config_ofoms_login = $configOfoms->config_ofoms_login;
        $this->config_ofoms_password = $configOfoms->config_ofoms_password;
        $this->config_ofoms_remote_host_name = $configOfoms->config_ofoms_remote_host_name;
        $this->config_ofoms_active = $configOfoms->config_ofoms_active;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ConfigOfomsRules::client();
    }

    public function attributeLabels()
    {
        return (new ConfigOfoms())->attributeLabels();
    }
}