<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 11:17
 */

namespace domain\forms\base;

use domain\models\base\ConfigCommon;
use domain\rules\base\ConfigCommonRules;
use yii\base\Model;

class ConfigCommonUpdateForm extends Model
{
    public $config_common_portal_mail;
    public $config_common_mail_administrators;

    public function __construct(ConfigCommon $configCommon, $config = [])
    {
        $this->config_common_portal_mail = $configCommon->config_common_portal_mail;
        $this->config_common_mail_administrators = $configCommon->config_common_mail_administrators;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ConfigCommonRules::client();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return (new ConfigCommon)->attributeLabels();
    }
}