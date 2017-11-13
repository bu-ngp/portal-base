<?php

namespace ngp\services\models;

use ngp\services\forms\ConfigOfomsUpdateForm;
use Yii;

/**
 * This is the model class for table "{{%config_ofoms}}".
 *
 * @property string $config_ofoms_id
 * @property string $config_ofoms_url
 * @property string $config_ofoms_url_prik
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
            [['config_ofoms_active'], 'integer'],
            [['config_ofoms_password'], 'required'],
            [['config_ofoms_password'], 'string'],
            [['config_ofoms_url', 'config_ofoms_url_prik', 'config_ofoms_login', 'config_ofoms_remote_host_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'config_ofoms_url' => Yii::t('ngp/ofoms', 'Config Ofoms Url'),
            'config_ofoms_url_prik' => Yii::t('ngp/ofoms', 'Config Ofoms Url Prik'),
            'config_ofoms_login' => Yii::t('ngp/ofoms', 'Config Ofoms Login'),
            'config_ofoms_password' => Yii::t('ngp/ofoms', 'Config Ofoms Password'),
            'config_ofoms_remote_host_name' => Yii::t('ngp/ofoms', 'Config Ofoms Remote Host Name'),
            'config_ofoms_active' => Yii::t('ngp/ofoms', 'Config Ofoms Active'),
        ];
    }

    public function edit(ConfigOfomsUpdateForm $form)
    {
        $this->config_ofoms_url = $form->config_ofoms_url;
        $this->config_ofoms_url_prik = $form->config_ofoms_url_prik;
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
