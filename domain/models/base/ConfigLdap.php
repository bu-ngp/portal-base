<?php

namespace domain\models\base;

use Yii;

/**
 * This is the model class for table "{{%config_ldap}}".
 *
 * @property string $config_ldap_id
 * @property string $config_ldap_host
 * @property string $config_ldap_port
 * @property integer $config_ldap_active
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
        return [
            [['config_ldap_port'], 'default', 'value' => 389],
            [['config_ldap_active'], 'default', 'value' => 0],
            [['config_ldap_port', 'config_ldap_active'], 'required'],
            [['config_ldap_port', 'config_ldap_active'], 'integer'],
            [['config_ldap_host'], 'string', 'max' => 255],
            [['config_ldap_host', 'config_ldap_port'], 'required', 'when' => function ($model) {
                return $model->config_ldap_active;
            }, 'enableClientValidation' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'config_ldap_id' => Yii::t('common/config-ldap', 'Config Ldap ID'),
            'config_ldap_host' => Yii::t('common/config-ldap', 'Config Ldap Host'),
            'config_ldap_port' => Yii::t('common/config-ldap', 'Config Ldap Port'),
            'config_ldap_active' => Yii::t('common/config-ldap', 'Config Ldap Active'),
        ];
    }

    public function editData($config_ldap_host, $config_ldap_port, $config_ldap_active)
    {
        $this->config_ldap_host = $config_ldap_host;
        $this->config_ldap_port = $config_ldap_port;
        $this->config_ldap_active = $config_ldap_active;
    }
}
