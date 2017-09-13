<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 11:17
 */

namespace domain\forms\base;

use domain\models\base\ConfigLdap;
use Yii;
use yii\base\Model;

class ConfigLdapUpdateForm extends Model
{
    public $config_ldap_host;
    public $config_ldap_port;
    public $config_ldap_active;

    private $configLdap;

    public function __construct(ConfigLdap $configLdap = null, $config = [])
    {
        $this->configLdap = $configLdap;
        $this->load($configLdap->attributes, '');
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return (new ConfigLdap())->rules();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return (new ConfigLdap())->attributeLabels();
    }
}