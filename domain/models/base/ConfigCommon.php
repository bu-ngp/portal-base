<?php

namespace domain\models\base;

use domain\forms\base\ConfigCommonUpdateForm;
use domain\rules\base\ConfigCommonRules;
use Yii;

/**
 * This is the model class for table "{{%config_common}}".
 *
 * @property string $config_common_portal_id
 * @property string $config_common_portal_mail
 * @property string $config_common_mail_administrators
 */
class ConfigCommon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%config_common}}';
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
        return [
            'config_common_id' => Yii::t('domain/config-common', 'Config Common ID'),
            'config_common_portal_mail' => Yii::t('domain/config-common', 'Config Common Portal Mail'),
            'config_common_mail_administrators' => Yii::t('domain/config-common', 'Config Common Mail Administrators'),
        ];
    }

    public function edit(ConfigCommonUpdateForm $form)
    {
        $this->config_common_portal_mail = $form->config_common_portal_mail;
        $this->config_common_mail_administrators = $form->config_common_mail_administrators;
    }
}
