<?php

namespace domain\models\base;

use domain\forms\base\ConfigLdapUpdateForm;
use domain\rules\base\ConfigLdapRules;
use Yii;

/**
 * This is the model class for table "{{%config_ldap}}".
 *
 * @property string $config_ldap_id
 * @property string $config_ldap_host
 * @property string $config_ldap_port
 * @property string $config_ldap_admin_login
 * @property integer $config_ldap_active
 * @property integer $config_ldap_admin_password
 */
class ConfigLdap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%config_ldap}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ConfigLdapRules::client();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'config_ldap_id' => Yii::t('domain/config-ldap', 'Config Ldap ID'),
            'config_ldap_host' => Yii::t('domain/config-ldap', 'Config Ldap Host'),
            'config_ldap_port' => Yii::t('domain/config-ldap', 'Config Ldap Port'),
            'config_ldap_active' => Yii::t('domain/config-ldap', 'Config Ldap Active'),
            'config_ldap_admin_login' => Yii::t('domain/config-ldap', 'Config Ldap Admin Login'),
            'config_ldap_admin_password' => Yii::t('domain/config-ldap', 'Config Ldap Admin Password'),
        ];
    }

    public function edit(ConfigLdapUpdateForm $form)
    {
        $this->config_ldap_host = $form->config_ldap_host;
        $this->config_ldap_port = $form->config_ldap_port;
        $this->config_ldap_admin_login = $form->config_ldap_admin_login;
        $this->config_ldap_admin_password = Yii::$app->security->encryptByPassword($form->config_ldap_admin_password, Yii::$app->request->cookieValidationKey);
        $this->config_ldap_active = $form->config_ldap_active;
    }

    public static function isLdapActive()
    {
        return self::findOne(1)->config_ldap_active;
    }
}
