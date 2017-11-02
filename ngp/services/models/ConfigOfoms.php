<?php

namespace ngp\services\models;

use ngp\services\forms\ConfigOfomsUpdateForm;
use Yii;

/**
 * This is the model class for table "{{%config_ofoms}}".
 *
 * @property string $config_ofoms_id
 * @property string $config_ofoms_host
 * @property string $config_ofoms_port
 * @property string $config_ofoms_login
 * @property string $config_ofoms_password
 * @property string $config_ofoms_remote_host_name
 * @property integer $config_ofoms_active
 */
class ConfigOfoms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%config_ofoms}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['config_ofoms_port', 'config_ofoms_active'], 'integer'],
            [['config_ofoms_password'], 'required'],
            [['config_ofoms_password'], 'string'],
            [['config_ofoms_host', 'config_ofoms_login', 'config_ofoms_remote_host_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'config_ofoms_id' => Yii::t('ngp/ofoms', 'Config Ofoms ID'),
            'config_ofoms_host' => Yii::t('ngp/ofoms', 'Config Ofoms Host'),
            'config_ofoms_port' => Yii::t('ngp/ofoms', 'Config Ofoms Port'),
            'config_ofoms_login' => Yii::t('ngp/ofoms', 'Config Ofoms Login'),
            'config_ofoms_password' => Yii::t('ngp/ofoms', 'Config Ofoms Password'),
            'config_ofoms_remote_host_name' => Yii::t('ngp/ofoms', 'Config Ofoms Remote Host Name'),
            'config_ofoms_active' => Yii::t('ngp/ofoms', 'Config Ofoms Active'),
        ];
    }

    public function edit(ConfigOfomsUpdateForm $form)
    {
        $this->config_ofoms_host = $form->config_ofoms_host;
        $this->config_ofoms_port = $form->config_ofoms_port;
        $this->config_ofoms_login = $form->config_ofoms_login;
        $this->config_ofoms_password = Yii::$app->security->encryptByPassword($form->config_ofoms_password, Yii::$app->request->cookieValidationKey);
        $this->config_ofoms_remote_host_name = $form->config_ofoms_remote_host_name;
        $this->config_ofoms_active = $form->config_ofoms_active;
    }

    public static function isOfomsActive()
    {
        return self::findOne(1)->config_ofoms_active;
    }
}
